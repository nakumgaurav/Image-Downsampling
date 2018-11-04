#!/usr/bin/env python
import sys, json
import pickle
with open('sigma.pickle', 'rb') as handle:
    sigma_dict = pickle.load(handle)

sigma_low = sigma_dict[min(sigma_dict, key=sigma_dict.get)]
sigma_highest = sigma_dict[max(sigma_dict, key=sigma_dict.get)]
gamma = 9

# determined by a separate experiment
thresh = 80.0 # intially, 53.5
sigma_highest /= 9
# emprically chosen
scale_factor = 0.6

sigma_fun = lambda sigma_val: ((((1 - scale_factor) / (thresh - sigma_low)) * (sigma_val - sigma_low) + scale_factor))

import time
current_milli_time = lambda: str(round(time.time() * 1000))

# Load the data that PHP sent us
imgs_selected = json.loads(sys.argv[1])
versions = imgs_selected["0"][0]
visitor_no = imgs_selected["0"][1]
landmarks = 1

import numpy as np
import os
import cv2
from PIL import Image

def noisy(image_mask, img_name):
	mean = 0
	sigma_val = sigma_dict[img_name + '.png']
	if (sigma_val < thresh):
		sigma = sigma_fun(sigma_val) * sigma_highest
	else:
		sigma = sigma_highest

	gauss = np.random.normal(mean, sigma, image_mask.shape)
	noise = gauss * image_mask/np.max(image_mask)
	return noise

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

def rand_dir_name(img_name):
	return img_name + '_' + current_milli_time() + '_' + str(visitor_no)

if __name__ == "__main__":
	random_img_locations = {}
	for k, val in imgs_selected.iteritems():
		k = int(k)
		# Need to skip the first iteration
		if(k==0):
			continue

		random_img_locations[k] = {}
		with open("test.txt", "w") as fp:
			fp.write(val)
		basepath1 = 'images_ds/' + val + '.png'
		new_rand_dir_name = rand_dir_name(val)

		os.mkdir('images_rand/' + new_rand_dir_name)
		basepath2 = 'images_rand/' + new_rand_dir_name + '/' + val + '-'
		basepath3 = 'images_mask_ds/' + val + '_'
		img = np.asarray(cv2.imread(basepath1, cv2.IMREAD_GRAYSCALE), dtype=np.float)


		img = np.asarray(Image.open(basepath1).convert('L'), dtype=np.float)
		
		for i in range(1,versions+1):
			random_img_locations[k][i] = {}
			for j in range(1,landmarks+1):
				img_mask = np.asarray(Image.open(basepath3 + landmark_decoder(j) + '.png').convert('L'), dtype=np.float)
				# img_mask = np.asarray(cv2.imread(basepath3 + landmark_decoder(j) + '.png', cv2.IMREAD_GRAYSCALE), dtype=np.float)
				
				noise = noisy(img_mask, val)

				nois_img_a = img + noise
				path_a = basepath2 + str(j) + 'a' + str(i)
				cv2.imwrite(path_a + '.png', nois_img_a)
				np.save(path_a + '_noise', noise)

				nois_img_b = img - noise
				path_b = basepath2 + str(j) + 'b' + str(i)
				cv2.imwrite(path_b + '.png', nois_img_b)

				random_img_locations[k][i][j] = [path_a + '.png', path_b + '.png']

	# with open("test.txt", "w") as fp:
	# 	fp.write("hello gau!")

	print json.dumps(random_img_locations)
