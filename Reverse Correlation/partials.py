################################################### VISITOR_NO < 1300 #####################################################

import mysql.connector

cnx = mysql.connector.connect(user="c1868219",
					password="gau",
					host="csmysql.cs.cf.ac.uk", 
					database="c1868219")

cur = cnx.cursor()

basepath_rand = 'images_rand/'

cur.execute('SELECT visitor_no FROM Responses GROUP BY (visitor_no) HAVING COUNT(image_id) >0 AND COUNT(image_id) <20')
visitors = cur.fetchall()

result = cur.execute(' SELECT T.iid AS iid, COUNT(T.vno) AS cnt \
						  FROM \
						  ( \
					  		SELECT DISTINCT R.visitor_no as vno, I.image_id as iid \
					  		FROM Responses AS R JOIN Images AS I \
					  		WHERE (R.visitor_no, I.image_id) NOT IN \
				  			( \
				  				SELECT visitor_no, image_id FROM Responses \
				  			) \
				 		  ) AS T \
						  GROUP BY (T.iid) \
						  ORDER BY T.iid ASC;')

# cur.execute('SELECT * FROM Responses \
# 			 WHERE visitor_no IN ( \
# 					SELECT visitor_no FROM Responses GROUP BY (visitor_no) HAVING COUNT(image_id) >0 AND COUNT(image_id) <20		\
# 			) AND visitor_no < 1300;')
# result = cur.fetchall()
###########################################################################################################################################################################################################

def users_partial():
	folders = next(os.walk(basepath_rand))[1]
	for rand_dir_name in folders:
		pieces = rand_dir_name.split('_')
		visitor_no = int(pieces[2])