print('Starting schedule generate program')
import requests
import dbconnect
from datetime import datetime

# true will pull from cbs, false will pull from files
request = False

week = range(1, 19)

# get html into files
def scrapehtml(week):
    url1 = 'https://www.cbssports.com/nfl/schedule/2022/regular/'
    url2 = '/'
    for i in week:
        url = url1 + str(i) + url2
        resp = requests.get(url)
        text = resp.text
        filepath = "week" + str(i) + "schedulecode.txt"
        f = open(filepath, "w")
        f.write(text)
        f.close()
        resp.connection.close()
        
if request:
    print('Scraping scedule data from the web into files')
    scrapehtml(week)
else:
    print('skipping web scrape')

# make nflteam dictionaries
nfldict = {}
nfldict.update({"Buffalo": "BUF"})
nfldict.update({"L.A. Rams": "LA"})
nfldict.update({"Baltimore": "BAL"})
nfldict.update({"N.Y. Jets": "NYJ"})
nfldict.update({"Cleveland": "CLE"})
nfldict.update({"Carolina": "CAR"})
nfldict.update({"Indianapolis": "IND"})
nfldict.update({"Houston": "HOU"})
nfldict.update({"Jacksonville": "JAX"})
nfldict.update({"Washington": "WAS"})
nfldict.update({"New England": "NE"})
nfldict.update({"Miami": "MIA"})
nfldict.update({"New Orleans": "NO"})
nfldict.update({"Atlanta": "ATL"})
nfldict.update({"Philadelphia": "PHI"})
nfldict.update({"Detroit": "DET"})
nfldict.update({"Pittsburgh": "PIT"})
nfldict.update({"Cincinnati": "CIN"})
nfldict.update({"San Francisco": "SF"})
nfldict.update({"Chicago": "CHI"})
nfldict.update({"Green Bay": "GB"})
nfldict.update({"Minnesota": "MIN"})
nfldict.update({"Kansas City": "KC"})
nfldict.update({"Arizona": "ARI"})
nfldict.update({"Las Vegas": "LV"})
nfldict.update({"L.A. Chargers": "LAC"})
nfldict.update({"N.Y. Giants": "NYG"})
nfldict.update({"Tennessee": "TEN"})
nfldict.update({"Tampa Bay": "TB"})
nfldict.update({"Dallas": "DAL"})
nfldict.update({"Denver": "DEN"})
nfldict.update({"Seattle": "SEA"})

# open files and make team arrays
def convert(datestring):
    datestring = datestring.strip()
    dateobj = datetime.strptime(datestring, '%b %d, %Y')
    day = dateobj.weekday()
    if day == 0:
        day = 'Mon'
    elif day == 1:
        day = 'Tue'
    elif day == 2:
        day = 'Wed'
    elif day == 3:
        day = 'Thu'
    elif day == 4:
        day = 'Fri'
    elif day == 5:
        day = 'Sat'
    elif day == 6:
        day = 'Sun'
    return day

allgames = []
print('opening files and parsing game data')
for i in week:
    filepath = "week" + str(i) + "schedulecode.txt"
    f = open(filepath, "r")
    text = f.read()
    f.close()

    index = 0
    week = i
    weeklygames = []
    while index < len(text):
        broken = False
        index = text.find('competitor', index)
        if index == -1:
            break
        index = text.find('"name": "', index) + len('"name": "')
        index2 = text.find('"', index)
        teamstring = text[index:index2]
        if teamstring in nfldict:
            awayteam = nfldict[teamstring]
        else:
            broken = True
        
        index = text.find('"name": "', index) + len('"name": "')
        index2 = text.find('"', index)
        teamstring = text[index:index2]
        if teamstring in nfldict:
            hometeam = nfldict[teamstring]
        else:
            broken = True
        
        # add code to get Day from date
        index = text.find('"startDate": "', index) + len('"startDate": "')
        index2 = text.find('"', index)
        datestring = text[index:index2]
        dateday = convert(datestring)
        
        if broken == True:
            break
        else:
            allgames.append((week, awayteam, hometeam, dateday))
            
    #allgames.append(weeklygames) - old code. not needed anymore 
    
#print(allgames)

# clear old sql data
conn = dbconnect.connect()
cursor = conn.cursor()
cursor.execute("delete from nflgames")
# insert new data
count = 0
for game in allgames:
    cursor.execute("insert into nflgames (week, hometeam, awayteam, day) values (" + str(game[0]) + ", '" + str(game[2]) + "', '" + str(game[1]) + "', '" + str(game[3]) + "')")
    count += 1
print(str(count) + ' games inserted into sql')

if conn is not None and conn.is_connected():
    conn.commit()
    conn.close()
    print('sql connection committed and closed')

