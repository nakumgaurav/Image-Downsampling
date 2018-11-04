#!/usr/bin/env python
import cgi
import cgitb; cgitb.enable()

form = cgi.FieldStorage()
visitor_no = form.getvalue('visitor_no')


if __name__ == "__main__":
	print 'Content-type:text/html\r\n\r\n'
	print '<html>'
	print '<head>'
	print '<title>Thank You</title>'
	print '</head>'
	print '<body>'
	print '<h2><center>Thank you for your responses.</center></h2>'

	print '</body>'
	print '</html>'