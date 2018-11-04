#!/usr/bin/env python
print 'Content-type:text/html\r\n\r\n'
import cgi
import cgitb; cgitb.enable()
form = cgi.FieldStorage()

### Get AJAX data
img_id = int(form.getvalue("img_id"))
visitor_no = int(form.getvalue("visitor_no"))
# visit_time = form.getvalue("visit_time")
from time import gmtime, strftime
response_time = strftime("%Y-%m-%d %H:%M:%S", gmtime())
normal_resp_time = int(form.getvalue("normal_resp_time"))
rc_resp_time = int(form.getvalue("rc_resp_time"))
ds_img_name = form.getvalue("ds_img_name")

### Connect to database
import mysql.connector

cnx = mysql.connector.connect(host="csmysql.cs.cf.ac.uk", 
					user="c1868219",
					password="gau",
					database="c1868219")

cur = cnx.cursor()

cur.execute('SELECT image_name FROM Images WHERE image_id={}'.format(img_id))
orig_img_name = cur.fetchall()[0][0]

truth_val = int(orig_img_name == ds_img_name)

### Insert into Evaluation table
cur.execute('INSERT INTO Evaluation (image_id, visitor_no, response_time, normal_resp_time, rc_resp_time, truth_val) VALUES \
({0}, {1}, "{2}",{3},{4}, {5});'.format(img_id, visitor_no, response_time, normal_resp_time, rc_resp_time, truth_val))

# fp = open("test.txt", "w")

# fp.write(str(type(img_id)) )

# fp.close()

cnx.commit()
cur.close()
cnx.close()