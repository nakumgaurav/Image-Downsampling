#!/usr/bin/env python
print 'Content-type:text/html\r\n\r\n'
import cgi
import cgitb; cgitb.enable()
form = cgi.FieldStorage()

### Get AJAX data
img_id = int(form.getvalue("img_id"))
visitor_no = int(form.getvalue("visitor_no"))
method_flag = int(form.getvalue("method_id"))
from time import gmtime, strftime
response_time = strftime("%Y-%m-%d %H:%M:%S", gmtime())
resp_time = int(form.getvalue("resp_time"))
ds_img_name = form.getvalue("ds_img_name")


### Connect to database
import mysql.connector

cnx = mysql.connector.connect(host="csmysql.cs.cf.ac.uk", 
					user="c1868219",
					password="gau",
					database="c1868219")

cur = cnx.cursor()

cur.execute('SELECT image_id FROM TestImages WHERE image_name="{}"'.format(ds_img_name))
ds_img_id = int(cur.fetchall()[0][0])

cur.execute('SELECT image_name FROM TrainImages WHERE image_id={}'.format(img_id))
orig_img_name = cur.fetchall()[0][0]
ds_img_name = ds_img_name.split('_')[0]
truth_val = int(orig_img_name == ds_img_name)


### Insert into Evaluation table
cur.execute('INSERT INTO Evaluation (visitor_no, response_time, ds_image_id, orig_image_id, resp_time, truth_val) VALUES \
({0}, "{1}", {2}, {3}, {4}, {5});'.format(visitor_no, response_time, ds_img_id, img_id, resp_time, truth_val))

cnx.commit()
cur.close()
cnx.close()