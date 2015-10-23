CREATE TABLE Client (
	fName VARCHAR(30) NOT NULL,
    lName VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    passwrd VARCHAR(50) NOT NULL
    
);

INSERT INTO Client(fName, lName, email, passwrd)
VALUES  ('Jorge', 'Perales', 'jorgelp94', 'test'),
		('Diego', 'Aleman', 'diegoaleman', 'test');