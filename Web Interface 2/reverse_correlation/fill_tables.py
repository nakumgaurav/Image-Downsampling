# Enter the names of 10 images in alphabetical order here
img_list = ["m-1-1","m-2-1","m-22-1","m-23-1","m-25-1","m-37-1","m-50-1","w-3-1","w-37-1","w-42-1"]

import mysql.connector

cnx = mysql.connector.connect(user="c1868219",
					password="gau",
					host="csmysql.cs.cf.ac.uk", 
					database="c1868219")

cur = cnx.cursor()

for img_name in img_list:
	cur.execute('INSERT INTO TrainImages (image_name) VALUES ("%s");' %(img_name))

for img_name in img_list:
	cur.execute('INSERT INTO TestImages (image_name) VALUES ("%s");' %(img_name + "_dpid"))

for img_name in img_list:
	cur.execute('INSERT INTO TestImages (image_name) VALUES ("%s");' %(img_name + "_lanc"))

for img_name in img_list:
	cur.execute('INSERT INTO TestImages (image_name) VALUES ("%s");' %(img_name + "_pix"))

for img_name in img_list:
	cur.execute('INSERT INTO TestImages (image_name) VALUES ("%s");' %(img_name + "_rc"))


cnx.commit()
cur.close()
cnx.close()