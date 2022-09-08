print('starting updatedb')
import dbconnect

conn = dbconnect.connect()
cursor = conn.cursor()
cursor.execute("select currentweek from globals")
results = cursor.fetchall()
dbweek = results[0][0]
# get existing finals
cursor.execute("select hometeam from nflgames where week = " + str(dbweek) + " and status = 'final'")
results = cursor.fetchall()
existingfinals = []
for game in results:
    existingfinals.append(game[0])
print(existingfinals)
if conn is not None and conn.is_connected():
    conn.commit()
    conn.close()
    print('sql connection committed and closed')

# call updater to update scores in db
import updatescores

# get updated finals
conn = dbconnect.connect()
cursor = conn.cursor()
cursor.execute("select hometeam from nflgames where week = " + str(dbweek) + " and status = 'final'")
results = cursor.fetchall()
updatedfinals = []
for game in results:
    updatedfinals.append(game[0])
print(updatedfinals)

# get added
newfinals = []
for game in updatedfinals:
    if game not in existingfinals:
        newfinals.append(game)
print(newfinals)

# call stored procedure on each game to update record
for game in newfinals:
    cursor.execute("CALL EndOfGame(" + str(dbweek) + ", " + game + ")")

# check how many games this week
cursor.execute("select count(*) from nflgames where week = " + str(dbweek))
results = cursor.fetchall()
numgames = results[0][0]

# execute stored procedure for end of the week
if numgames == len(updatedfinals) and numgames != len(existingfinals):
    cursor.execute("CALL EndOfWeek(" + str(dbweek) + ")");


if conn is not None and conn.is_connected():
    conn.commit()
    conn.close()
    print('sql connection committed and closed')

