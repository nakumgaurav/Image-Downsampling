import os
import shutil

import mysql.connector

alpha = 20

cnx = mysql.connector.connect(user="c1868219",
					password="gau",
					host="csmysql.cs.cf.ac.uk", 
					database="c1868219")

cur = cnx.cursor()

basepath_rand = 'images_rand/'
basepath_done = 'images_done/'

# for those users who have completed the survey
def users_started():
	# First, find all such users
	cur.execute('SELECT DISTINCT visitor_no FROM Responses WHERE sel_string IS NOT NULL;')
	result = cur.fetchall()
	# visitors = []
	# for tuples in result:
	# 	visitors.append(tuples[0])


	visitors = range(3652,3672) + [3673,3674,3675,3677,3679,3680,3684,3708] + range(3686,3692) + range(3693,3700) + range(3701,3707) + range(3710,3767) + range(3768,3773)
	# visitors = range(3413,3457) + range(3458,3475) + range(3476,3518) + range(3519,3535) + range(3536,3542) + range(3543,3547) + range(3548,3573) + [3575,3576] + range(3578,3596) + range(3597,3636) + range(3637,3648) + [3649,3650]
	# visitors = range(2976,2980) + range(2981,3002) + [3003,3004] + range(3006,3019) + range(3020,3079) + range(3080,3122) + range(3123,3182) + range(3183,3187) + range(3188,3298) + range(3299,3377) + range(3378,3381) + range(3382,3412)
	# visitors = range(2807,2811) + range(2812,2975)
	# visitors = range(2517,2538) + range(2539,2556) + range(2557,2579) + range(2580,2605) + range(2606,2614) + range(2615,2662) + range(2666,2683) + [2663,2664] + range(2684,2747) + range(2748,2806)
	# visitors = range(2243,2273) + range(2274,2284) + range(2285,2311) + range(2312,2322) + range(2323,2332) + range(2333,2339) + [2340,2341] + range(2344,2355) + range(2356,2467) + range(2468,2477) + range(2478,2505) + range(2506,2516)	
	# visitors = range(1964,1977) + range(1978,1990) + range(1991,1999) + range(2000,2025) + range(2026,2037) + range(2038,2048) + range(2049,2056) + range(2057,2061) + range(2062,2093) + range(2094,2131) + range(2133,2166) + range(2167,2211) + range(2212,2232) + range(2233,2240) + [2241]	
	# visitors = [1781,1782,1813,1815,1834] + range(1784,1797) + range(1799,1802) + range(1807,1811) + range(1817,1832) + range(1838,1842) + range(1844,1848) + range(1849,1861) + range(1862,1869) + range(1870,1891) + range(1892,1905) + range(1906,1913) + range(1914,1944) + range(1945,1963)
	# visitors = range(1773,1779)
	# visitors = [1764,1765,1767,1768,1769,1770,1771]
	# visitors = range(1596,1619) + range(1620,1627) + range(1628,1642) + range(1643,1652) + range(1653,1664) + [1665,1666] + range(1668,1673) + [1674,1675] + range(1678,1684) + range(1685,1701) + range(1702,1727) + [1728] + range(1730,1750) + [1751,1752,1754,1760,1762]
	# visitors = range(1412,1427) + [1428,1429] + range(1432,1449) + range(1451,1455) + range(1456,1461) + range(1463,1467) + range(1468,1472) + range(1473,1500) + range(1501,1541) + range(1542,1547) + range(1548,1565) + range(1566,1569) + range(1570,1573) + range(1574,1595)
	# visitors = range(1308,1312) + [1313] + range(1315,1322) + range(1323,1331) + range(1332,1335) + [1336,1337] + range(1339,1380) + [1381,1382] + range(1385,1404) + range(1405,1409) + [1410]
	# visitors = range(1265, 1270) + range(1271, 1296) + range(1297, 1299) + range(1300, 1307)
	# visitors = range(1232, 1265)
	# visitors = range(1013, 1061) + range(1062, 1092) + [1093] + range(1095, 1132) + range(1133, 1232)
	# visitors = range(954, 965) + range(966, 981) + range(982, 987) + range(988, 997) + range(998, 1012)
	# visitors = range(511, 520) + range(492, 510) + range(417, 491) + range(403, 416) + range(397, 402) + range(382, 396)
	# visitors = [101, 103, 104, 106, 108, 114, 115, 116, 118, 124, 125, 126, 127, 129, 135, 134, 141, 143, 146, 149, 150, 152, 153, 154, 155, 156, 163, 164, 168, 169, 170, 171, 172, 173, 175, 176, 177, 178, 179, 180]30
	# visitors = [98, 97, 96, 92, 91, 90, 86, 84, 81, 79, 78, 77, 76, 73, 72, 71, 70]

	# Then, provided their folders exist in images_rand, move them to images_done
	folders = next(os.walk(basepath_rand))[1]
	for rand_dir_name in folders:
		pieces = rand_dir_name.split('_')
		visitor_no = int(pieces[2])
		if(visitor_no in visitors):
			shutil.move(basepath_rand + rand_dir_name, basepath_done)

# Transfer only if the user has made responses for all images
def users_completed():
	# Then, provided their folders exist in images_rand, move them to images_done
	folders = next(os.walk(basepath_rand))[1]
	for rand_dir_name in folders:
		pieces = rand_dir_name.split('_')
		image_name = pieces[0]
		visitor_no = int(pieces[2])
		# cur.execute('SELECT count(image_id) FROM Responses WHERE visitor_no = {} AND sel_string IS NOT NULL;'.format(visitor_no))
		# count = int(cur.fetchall()[0][0])
		# # write_this = str(result) + str(visitor_no) + 
		# # with open("test.txt", "w") as fp:
		# # 	fp.write(write_this)
		# if(count == alpha):
			# shutil.move(basepath_rand + rand_dir_name, basepath_done)
		if os.path.isfile(basepath_rand + rand_dir_name + '/' + image_name + '_avgNoise.npy'):
			# if(visitor_no < 1771):
			shutil.move(basepath_rand + rand_dir_name, basepath_done)

users_started()
# users_completed()
