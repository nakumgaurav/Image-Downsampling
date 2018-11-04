#!/usr/bin/env python
import os
import shutil
import numpy as np
import cv2
from PIL import Image
############################################################################################################################################################################################################
### Global Data
landmarks = 1
expressions = 1
pairs = 5
versions = pairs/landmarks
num_images = 20
# found through a query in partials.py
# max_updates = 29

# path where all original (non-noisy) downsampled images are stored
basepath_ds = 'images_ds/'
# path where all random images are stored
basepath_rand = 'images_rand/'
# path where all average images are stored
basepath_avg = 'images_avg+/'
# path where all images' masks are stored
basepath_mask = 'images_mask_ds/'
# path where all images' background masks are stored
basepath_background_mask = 'background_masks_ds/'
# 
basepath_done = 'images_done/'

import mysql.connector

cnx = mysql.connector.connect(user="c1868219",
					password="gau",
					host="csmysql.cs.cf.ac.uk", 
					database="c1868219")

cur = cnx.cursor()

folders = next(os.walk(basepath_rand))[1]

cur.execute('SELECT * FROM Responses \
			 WHERE visitor_no IN ( \
					SELECT visitor_no FROM Responses WHERE visitor_no<1300 GROUP BY (visitor_no) \
					HAVING COUNT(image_id)>0 AND COUNT(image_id)<20 \
			) ORDER BY visitor_no DESC;')
result = cur.fetchall()

# num_updates = {}
# for i in xrange(1, num_images+1):
# 	num_updates[i] = 0



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
def find_average_over_choices(img_name, weights, rand_dir_name, choice_list):
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
	rand_img_path = basepath_rand + rand_dir_name + '/'

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
def construct_weights_list_perImage(img_name):
	weights_list_perImage = []
	for j in xrange(1, landmarks + 1):
		mask_path = basepath_mask + img_name + '_' + landmark_decoder(j) + '.png'
		img_mask = np.asarray(Image.open(mask_path).convert('L'), dtype=np.float)
		# img_mask = np.asarray(cv2.imread(img_path, cv2.IMREAD_GRAYSCALE), dtype = np.float)
		norm_img_mask = img_mask / np.max(img_mask)
		weights_list_perImage.append(norm_img_mask)
	return weights_list_perImage

def update_running_noise(img_name, N, curr_noise):
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

def main_fun(img_name, rand_dir_name, choice_list):
	# weights_list stores the landmarks weights for each image
	weights = construct_weights_list_perImage(img_name)

	### Find average noise
	avg_noise = find_average_over_choices(img_name, weights, rand_dir_name, choice_list)
	# Find N (the number of images so far used to calculate the present average)
	cur.execute('SELECT num_updates FROM Images3 WHERE image_name="%s";' %img_name)
	N = int(cur.fetchall()[0][0])

	### Update running averages
	ds_img_path = basepath_ds + img_name + '.png'
	ds_img = np.asarray(cv2.imread(ds_img_path, cv2.IMREAD_GRAYSCALE), dtype=np.float)

	updated_running_noise = update_running_noise(img_name, N, avg_noise)
	updated_avg_img = ds_img + updated_running_noise
	# updated_avg_img = update_running_average(ds_img+avg_noise, N, img_name)
	save_path = basepath_avg + img_name + '.png'
	cv2.imwrite(save_path, updated_avg_img)

	### Update Images3 table
	cur.execute('UPDATE Images3 SET num_updates = num_updates + 1 WHERE image_name = "%s";' %img_name)

	
# cnt = 0
for record in result:
	img_id = int(record[0])
	cur.execute('SELECT image_name FROM Images3 WHERE image_id={}'.format(img_id))
	img_name = cur.fetchall()[0][0]
	response_time = record[1]
	visitor_no = int(record[2])
	sel_string = record[3]
	choice_list = sel_string.split('_')[:5]
	# cnt += 1

	# flag = 0
	for rand_dir_name in folders:
		pieces = rand_dir_name.split('_')
		dir_img_name = pieces[0]
		dir_visitor_no = int(pieces[2])
		if(visitor_no == dir_visitor_no and img_name == dir_img_name):
			# print visitor_no, choice_list, img_id
			main_fun(img_name, rand_dir_name, choice_list)
			shutil.move(basepath_rand + rand_dir_name, basepath_done)
			if(os.path.isfile(basepath_done + rand_dir_name + '/' + img_name + '_avgNoise.npy')):
				# cnt += 1
				break
			# flag = 1

	# if(not flag):
		# print visitor_no, img_name

# print cnt
cnx.commit()
cur.close()
cnx.close()

### 		TRUNCATE TABLE `Images3`;                     		### 

### 		INSERT INTO `Images3` SELECT * FROM `Images`; 		###

			# SELECT * FROM Responses 
			# WHERE visitor_no IN 
			# ( 
			# 	SELECT visitor_no FROM Responses 
			# 	WHERE visitor_no<1300 
			# 	GROUP BY (visitor_no)
			# 	HAVING COUNT(image_id)>0 AND COUNT(image_id)<20 
			# ) ORDER BY visitor_no DESC;