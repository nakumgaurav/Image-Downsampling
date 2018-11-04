#!/usr/bin/env python
print 'Content-type:text/html\r\n\r\n'
import cgi
import cgitb; cgitb.enable()
form = cgi.FieldStorage()

import numpy as np
import cv2
from PIL import Image
############################################################################################################################################################################################################
### Global Data
landmarks = 1
expressions = 1

## Get AJAX data
img_path = form.getvalue("path")
img_id = int(form.getvalue("img_id"))
img_name = form.getvalue("img_name")
choiceList = form.getvalue("choice_list")

from time import gmtime, strftime
response_time = strftime("%Y-%m-%d %H:%M:%S", gmtime())

## Preprocesss the data
choiceList = choiceList.split(',')
choice_list = []
for i, choice in enumerate(choiceList):
	if(i==0):
		choice = choice.replace("[", "")
	elif(i==len(choiceList)-1):
		choice = choice.replace("]", "")
	choice_list.append(int(choice))
pairs = len(choice_list)/2

versions = pairs/landmarks

pieces = img_path.split('/')
rand_dir_name = pieces[1]
visitor_no = int(rand_dir_name.split('_')[2])

pair_dict = {}

# Construct sel_string
m = 0
while(m < 2*pairs):
	pair_no = choice_list[m]
	m += 1
	pair_sel = choice_list[m]
	img_no = 2*(pair_no-1) + pair_sel
	pair_dict[pair_no] = img_no
	m += 1

sel_string = ""
for pair_no, img_no in pair_dict.iteritems():
	if(pair_no == pairs):
		sel_string += str(img_no)
	else:
		sel_string += str(img_no) + "_"
	# img_path = basepath + img_name + '-' + find_name_from_img_no(int(img_no)) + '.png'

choice_list = sel_string.split('_')[:5]

# path where all original (non-noisy) downsampled images are stored
basepath_ds = '../images_ds/'
# path where all random images are stored
basepath_rand = '../images_rand/'
# path where all average images are stored
basepath_avg = '../images_avg/'
# path where all images' masks are stored
basepath_mask = '../images_mask_ds/'
# path where all images' background masks are stored
basepath_background_mask = '../background_masks_ds/'

basepath = basepath_rand + '/' + rand_dir_name + '/'

############################################################################################################################################################################################################
### Functions

def landmark_decoder(num):
	if(num==1):
		return "all"
	# if(num==1):
	# 	return "chin"
	# elif(num==2):
	# 	return "eyebrows"
	# elif(num==3):
	# 	return "eyes"
	# elif(num==4):
	# 	return "jaw"
	# elif(num==5):
	# 	return "mouth"
	# elif(num==6):
	# 	return "nose"

# given the img_no (ranging from 1 to 2*pairs) find the image sub name
def find_name_from_img_no(img_no):
	version_no = (img_no-1)/(2*landmarks) + 1
	if(img_no % 2):
		a_b = 'a'
	else:
		a_b = 'b'
	landmark_no = ((img_no-1)/2) % landmarks + 1
	# landmark_no = ((img_no - (2*(version_no-1)*landmarks)) + 1)/2
	image_name = str(landmark_no) + a_b + str(version_no)
	return image_name

#	    1           2          3		     4			5			6			7			8
#	(x-abc-1a1, x-abc-1b1); (x-abc-2a1, x-abc-2b1); (x-abc-3a1, x-abc-3b1); (x-abc-4a1, x-abc-4b1);
#		9			10		   11			12			13			14			15			16
#	(x-abc-1a2, x-abc-1b2); (x-abc-2a2, x-abc-2b2); (x-abc-3a2, x-abc-3b2); (x-abc-4a2, x-abc-4b2);


# this gives the average of choices made by the current user for one image
# Parameters: 1. loc is the name of the random dir (within images_rand) where
#			  the generated random images of this image are located
# 			  2. weights is the list of weights forversions this img
def find_average_over_choices(weights):
	### Weighted Averages
	ds_img_path = basepath_ds + img_name + '.png'
	# ds_img = np.asarray(Image.open(ds_img_path).convert('L'), dtype=np.float)
	ds_img = np.asarray(cv2.imread(ds_img_path, cv2.IMREAD_GRAYSCALE), dtype=np.float)

	# get sum of weights in tot
	tot = np.zeros_like(ds_img)
	for m, val in enumerate(weights):
		tot += val

	# add background mask weights to the face mask weights
	background_mask_path = basepath_background_mask + img_name + '.png'
	background_mask = np.asarray(Image.open(background_mask_path).convert('L'), dtype=np.float)
	background_mask /= np.max(background_mask)
	tot += background_mask

	# Get the final sum of weights matrix
	tot *= versions

	sum_nois_imgs = np.zeros_like(ds_img)
	rand_img_path = basepath

	for i, img_no in enumerate(choice_list):
		flag = 0
		name = find_name_from_img_no(int(img_no))
		if ('a' not in name):
			name = name.replace('b', 'a')
			flag = 1
		rand_img_name = img_name + '-' + name + '_noise.npy'
		nois_img = np.load(rand_img_path + rand_img_name)
		if(flag):
			nois_img = -nois_img		
		sum_nois_imgs += nois_img

	# avg_img = ds_img + np.divide(sum_nois_imgs, tot)
	avg_noise = np.divide(sum_nois_imgs, tot)

	np.save(rand_img_path + img_name + '_avgNoise.npy', avg_noise)

	# return avg_img
	return avg_noise

# gets the weights (normalized image masks) for a single image with name img_name
def construct_weights_list_perImage():
	weights_list_perImage = []
	for j in xrange(1, landmarks + 1):
		mask_path = basepath_mask + img_name + '_' + landmark_decoder(j) + '.png'
		img_mask = np.asarray(Image.open(mask_path).convert('L'), dtype=np.float)
		# img_mask = np.asarray(cv2.imread(img_path, cv2.IMREAD_GRAYSCALE), dtype = np.float)
		norm_img_mask = img_mask / np.max(img_mask)
		weights_list_perImage.append(norm_img_mask)
	return weights_list_perImage

def update_running_noise(N, curr_noise):
	# the given image may not yet have an average (i.e., this is the first user who has been assigned this image)
	if(N>0):
		avg_n = np.load(basepath_avg + img_name + '_noise.npy')
		avg_n_plus_1 = (N * avg_n + curr_noise) / (N + 1)
		# print '<p>{},{}</p>'.format(img_name, N)
		np.save(basepath_avg + img_name + '_noise.npy', avg_n_plus_1)
	else:
		avg_n_plus_1 =  curr_noise
		np.save(basepath_avg + img_name + '_noise.npy', avg_n_plus_1)

	return avg_n_plus_1

if __name__ == "__main__":
	### Connect to database
	import mysql.connector

	cnx = mysql.connector.connect(user="c1868219",
						password="gau",
						host="csmysql.cs.cf.ac.uk", 
						database="c1868219")

	cur = cnx.cursor()

	### Update Responses table
	cur.execute('INSERT INTO Responses (image_id, response_time, visitor_no, sel_string) VALUES ({},"{}",{},"{}");'.format(img_id, response_time, visitor_no, sel_string))

	# weights_list stores the landmarks weights for each image
	weights = construct_weights_list_perImage()

	### Find average noise
	avg_noise = find_average_over_choices(weights)
	# Find N (the number of images so far used to calculate the present average)
	cur.execute('SELECT num_updates FROM Images WHERE image_name="%s";' %img_name)
	N = int(cur.fetchall()[0][0])

	### Update running averages
	ds_img_path = basepath_ds + img_name + '.png'
	ds_img = np.asarray(cv2.imread(ds_img_path, cv2.IMREAD_GRAYSCALE), dtype=np.float)

	updated_running_noise = update_running_noise(N, avg_noise)
	updated_avg_img = ds_img + updated_running_noise
	# updated_avg_img = update_running_average(ds_img+avg_noise, N, img_name)
	save_path = basepath_avg + img_name + '.png'
	cv2.imwrite(save_path, updated_avg_img)

	### Update Images table
	cur.execute('UPDATE Images SET num_updates = num_updates + 1 WHERE image_name = "%s";' %img_name)

	cnx.commit()
	cur.close()
	cnx.close()