print('Starting live game data scraping program')
import requests
import dbconnect
from datetime import datetime
import json
url = 'http://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard'
request = False

# scrape json from web into text file
filepath = 'espnjson.txt'
if request:
    print('Scraping games data from the web into file')
    resp = requests.get(url)
    text = resp.text
    f = open(filepath, "w")
    f.write(text)
    f.close()
    resp.connection.close()
else:
    print('skipping web scrape')

#open text file and make it json data
f = open(filepath, "r")
text = f.read()
f.close()

data = json.loads(text)

#check week against sql week
dataweek = data['week']['number']
print(dataweek)

conn = dbconnect.connect()
cursor = conn.cursor()
cursor.execute("select currentweek from globals")
results = cursor.fetchall()
print(results)
dbweek = results[0][0]
if dbweek != dataweek:
    print('DBweek: ' + str(dbweek) + ' and Dataweek: ' + str(dataweek) + ' do not match. Cannot update scores in database')
else:
    # get data from object
    events = data['events']
    
    def checkabbr(abbr):
        if abbr == 'LAR':
            abbr = 'LA'
        elif abbr == 'WSH':
            abbr = 'WAS'
        return abbr
    
    for event1 in events:
        #event1 = events[5]
        
        # 1 is home, 2 is away
        score1 = event1['competitions'][0]['competitors'][0]['score']
        print(score1)
        abbr1 = event1['competitions'][0]['competitors'][0]['team']['abbreviation']
        abbr1 = checkabbr(abbr1)
        print(abbr1)
        score2 = event1['competitions'][0]['competitors'][1]['score']
        print(score2)
        abbr2 = event1['competitions'][0]['competitors'][1]['team']['abbreviation']
        abbr2 = checkabbr(abbr2)
        print(abbr2)
        dict = {abbr1:score1, abbr2:score2}
        #awayscore = dict[awaystring]
        #homescore = dict[homestring]
        status = event1['competitions'][0]['status']
        clock = status['clock']
        displayClock = status['displayClock']
        period = status['period']
        
        fulldate = status['type']['detail']
        
        state = status['type']['state']
        if state == "post":
            sqlstatus = "final"
            #cursor.execute stored procedure end of game. change record of nfl team
            cursor.execute("update nflgames set status = '" + sqlstatus + "', homescore = " + str(score1) + ", awayscore = " + str(score2) 
            + ", quarter = " + str(period) + ", clock = '" + str(displayClock) 
            + "' where week = " + str(dataweek) + " and hometeam = '" + abbr1 + "' and awayteam = '" + abbr2 + "'")
        elif state == "pre":
            sqlstatus = "upcoming"
            kickoffstring = fulldate
            #kickoffobj = datetime.strptime(fulldate, '%a, %B %dth at %I:%M %p %Z')
            kickoffobj = datetime.strptime(fulldate[0:len(fulldate) - 4], '%a, %B %dth at %I:%M %p')
            kickofftime = kickoffobj.time()
            kickoffday = fulldate[0:3]
            #sqlstring = 'update nflgames set day = ' + kickoffday + ', kickofftime = ' + str(kickofftime) + ', status = ' + sqlstatus + ', homescore = ' + str(score1) + ', awayscore = ' + str(score2) + ', quarter = ' + str(period) + ', clock = ' + str(displayClock) + ' where week = ' + str(dataweek) + ' and hometeam = ' + abbr1 + ' and awayteam = ' + abbr2
            #print(sqlstring)
            cursor.execute("update nflgames set day = '" + kickoffday + "', kickofftime = '" + str(kickofftime) 
            + "', status = '" + sqlstatus + "', homescore = " + str(score1) + ", awayscore = " + str(score2) 
            + ", quarter = " + str(period) + ", clock = '" + str(displayClock) 
            + "' where week = " + str(dataweek) + " and hometeam = '" + abbr1 + "' and awayteam = '" + abbr2 + "'")
            print(kickofftime)
        elif state == "in":
            sqlstatus = "ongoing"
            cursor.execute("update nflgames set status = '" + sqlstatus + "', homescore = " + str(score1) + ", awayscore = " + str(score2) 
            + ", quarter = " + str(period) + ", clock = '" + str(displayClock) 
            + "' where week = " + str(dataweek) + " and hometeam = '" + abbr1 + "' and awayteam = '" + abbr2 + "'")
        
        
        shortdate = status['type']['shortDetail']
        
        
        print((abbr1, score1, abbr2, score2, clock, displayClock, period, sqlstatus, fulldate))

# check the db for a complete week
cursor.execute("select count(*) from nflgames where week = " + str(dbweek))
results = cursor.fetchall()
totalgames = results[0][0]
cursor.execute("select count(*) from nflgames where status = 'final' and week = " + str(dbweek))
results = cursor.fetchall()
finalgames = results[0][0]

if totalgames == finalgames:
    print("week over")

if conn is not None and conn.is_connected():
    conn.commit()
    conn.close()
    print('sql connection committed and closed')

