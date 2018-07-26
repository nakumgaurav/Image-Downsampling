# First find all those visitor_no (for each image), who have completed the survey using each of the 4 methods
# and store the result in a dict
import csv

image_visitor_dict = {}

num_imgs = 10
with open("counter_file.txt","r") as fp:
	visitors = int(fp.read())
methods = 4

import mysql.connector

cnx = mysql.connector.connect(user="c1868219",
					password="gau",
					host="csmysql.cs.cf.ac.uk", 
					database="c1868219")

cur = cnx.cursor()

for ds_image_id in xrange(1,num_imgs+1):
	image_visitor_dict[ds_image_id] = []
	for visitor_no in xrange(1,visitors+1):
		cur.execute("SELECT COUNT(*) FROM Evaluation WHERE (visitor_no = {}) AND (ds_image_id = {} \
		OR ds_image_id = {} OR ds_image_id = {} OR ds_image_id = {});".format(visitor_no,ds_image_id,ds_image_id+10,
			ds_image_id+20,ds_image_id+30))

		valid = int(cur.fetchall()[0][0])
		if(valid==4):
			image_visitor_dict[ds_image_id].append(visitor_no)

# for ds_image_id in xrange(1,num_imgs+1):
# 	print len(image_visitor_dict[ds_image_id])

# result is a dict storing the percentage of correct responses for each image for each method
# result[1][1] gives the percetage of correct responses for image_id 1 using method 1 (dpid)
result = {}

for ds_image_id in xrange(1,num_imgs+1):
	result[ds_image_id] = {}
	for method_no in xrange(1,methods+1):
		result[ds_image_id][method_no] = 0

for ds_image_id in xrange(1,num_imgs+1):
	visitor_list = image_visitor_dict[ds_image_id]
	num_responses = len(visitor_list)
	for visitor_no in visitor_list:
		for method_no in xrange(1,methods+1):
			ds_img_id = ds_image_id + 10*(method_no-1)
			cur.execute('SELECT truth_val FROM Evaluation WHERE visitor_no={} AND ds_image_id = {}'.format(visitor_no, ds_img_id))
			truth_val = int(cur.fetchall()[0][0])
			result[ds_image_id][method_no] += truth_val

	for method_no in xrange(1,methods+1):
		result[ds_image_id][method_no] *= 100/num_responses

# print result

with open("result.csv", "wb") as fp:
	w = csv.writer(fp)
	methods = [1,2,3,4]
	for key in result.keys():
		w.writerow([key] + [result[key][method_no] for method_no in methods])

cur.close()
cnx.close()