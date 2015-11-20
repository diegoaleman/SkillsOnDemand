CREATE TABLE Client (
	fName VARCHAR(30) NOT NULL,
    lName VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    passwrd VARCHAR(50) NOT NULL,
	yearBdate INT,
	country VARCHAR(250),
	city VARCHAR(250),
	website VARCHAR(250),
	phone INT,
	university VARCHAR(250),
	interests VARCHAR(250),
	more VARCHAR(250)
);


CREATE TABLE Skill(
	skillId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(250) NOT NULL,
	description VARCHAR(250) NOT NULL,
	category VARCHAR(250) NOT NULL,
	quantity INT NOT NULL,
	FOREIGN KEY (email)
		REFERENCES Client (email)
		ON DELETE CASCADE,
	email VARCHAR(250) NOT NULL,
	category VARCHAR(250) NOT NULL,
    quantity INT NOT NULL,
	FOREIGN KEY (email)
		REFERENCES Client (email)
		ON DELETE CASCADE

);

CREATE TABLE Cart(
	orderId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(250) NOT NULL,
	skill INT NOT NULL,
	quantity INT NOT NULL,
	status CHAR(1) NOT NULL,
	FOREIGN KEY (email) 
		REFERENCES Client (email)
		ON DELETE CASCADE,
	FOREIGN KEY (skill) 
		REFERENCES Skill (skillId)
		ON DELETE CASCADE
);
CREATE TABLE Message(
	messageId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	sentTo VARCHAR(50) NOT NULL,
	sentFrom VARCHAR(50) NOT NULL,
	messageDate datetime NOT NULL,
	message VARCHAR(250) NOT NULL,
	status VARCHAR(1) NOT NULL,
	FOREIGN KEY (sentTo)
		REFERENCES Client (email)
		ON DELETE CASCADE,
	FOREIGN KEY (sentFrom)
		REFERENCES Client (email)
		ON DELETE CASCADE
);
