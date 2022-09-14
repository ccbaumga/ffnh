import dbconnect
import string

reg_sql_codes = ('tableCreation.sql', 'createTeams.sql')
routines = ('gameover.sql', 'instancepointsthisweek.sql', 'instancepointsseason.sql', 'teampointsthisweek.sql', 'weekover.sql', 'startUpdate.sql')

def parse_sql_delim(text):
    dropline = ''
    createline = ''
    pos = text.find('DELIMITER')
    delim = text[pos + len('DELIMITER') + 1:].split()[0]
    startpos = text.find(delim) + len(delim)
    pos2 = text.find(delim, pos + len('DELIMITER') + 1 + len(delim))
    dropline = text[:pos]
    createline = text[startpos:pos2]
    print((len(text), pos, pos2, delim))
    if pos == -1 or pos2 == -1:
        return 'failed-improper format'
    return (dropline, createline)

con = dbconnect.connect()

cur = con.cursor(dictionary=True)

for reg_sql_code in reg_sql_codes:
    with open(reg_sql_code, 'r') as sql_file:
        result_iterator = cur.execute(sql_file.read(), multi=True)
        for res in result_iterator:
            print("Running query: ", res)  # Will print out a short representation of the query
            print(f"Affected {res.rowcount} rows" )

        con.commit()  # Remember to commit all your changes!

for routine in routines:
    with open(routine, 'r') as sql_file:
        text = sql_file.read()
        parse = (parse_sql_delim(text))
        if parse == 'failed-improper format':
            print(parse)
        else:
            cur.execute(parse[0])
            cur.execute(parse[1])
            print()
            print(parse[0])
            print()
            print(parse[1])
            print()
            con.commit()
    
if con is not None and con.is_connected():
    con.commit()
    con.close()
    print('sql connection committed and closed')
    
import generateSchedule