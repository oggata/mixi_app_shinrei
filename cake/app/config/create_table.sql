create table elements(
id int not null auto_increment,
genre_id int,
txt varchar(250),
PRIMARY KEY (`id`)
)TYPE=InnoDB AUTO_INCREMENT=1;

create table announces (
id int not null auto_increment,
member_id int,
txt longtext,
PRIMARY KEY (`id`)
)TYPE=InnoDB AUTO_INCREMENT=1;

--genre_id 1:いつ 2:どこで 3:だれと 4:なにを 5:どうする
insert into elements (genre_id,txt)values(1,'今日');
insert into elements (genre_id,txt)values(1,'明日');
insert into elements (genre_id,txt)values(1,'3日後');
insert into elements (genre_id,txt)values(1,'100年後');
insert into elements (genre_id,txt)values(1,'今すぐ');
insert into elements (genre_id,txt)values(1,'気が向いたら');
insert into elements (genre_id,txt)values(1,'明日晴れたら');

insert into elements (genre_id,txt)values(2,'東京');
insert into elements (genre_id,txt)values(2,'近所');
insert into elements (genre_id,txt)values(2,'自宅');
insert into elements (genre_id,txt)values(2,'親戚の家');
insert into elements (genre_id,txt)values(2,'野外');
insert into elements (genre_id,txt)values(2,'屋内');
insert into elements (genre_id,txt)values(2,'ドーム球場');
insert into elements (genre_id,txt)values(2,'コンサート会場');
insert into elements (genre_id,txt)values(2,'飲み屋');


insert into elements (genre_id,txt)values(3,'友達');
insert into elements (genre_id,txt)values(3,'父親');
insert into elements (genre_id,txt)values(3,'自分');
insert into elements (genre_id,txt)values(3,'姉');
insert into elements (genre_id,txt)values(3,'親友');
insert into elements (genre_id,txt)values(3,'犬');



insert into elements (genre_id,txt)values(4,'パチンコ');
insert into elements (genre_id,txt)values(4,'運動会');
insert into elements (genre_id,txt)values(4,'チャット');
insert into elements (genre_id,txt)values(4,'運動会');
insert into elements (genre_id,txt)values(4,'パチンコ');
insert into elements (genre_id,txt)values(4,'運動会');
insert into elements (genre_id,txt)values(4,'パチンコ');
insert into elements (genre_id,txt)values(4,'運動会');

を

insert into elements (genre_id,txt)values(5,'食べる');
insert into elements (genre_id,txt)values(5,'ダイエットする');
insert into elements (genre_id,txt)values(5,'飼う');
insert into elements (genre_id,txt)values(5,'投げる');
insert into elements (genre_id,txt)values(5,'四の字固めする');
insert into elements (genre_id,txt)values(5,'困る');
insert into elements (genre_id,txt)values(5,'蹴る');
insert into elements (genre_id,txt)values(5,'チャットする');
insert into elements (genre_id,txt)values(5,'食べる');

create table questions(
   `id` int NOT NULL auto_increment,
    member_id int,
    answer_1 int,
    answer_2 int,
    answer_3 int,
    answer_4 int,
    answer_5 int,
    answer_6 int,
    answer_7 int,
    answer_8 int,
    answer_9 int,
    answer_10 int,
    insert_date date,
    update_date date,
  PRIMARY KEY (`id`)
)TYPE=InnoDB AUTO_INCREMENT=1;

create table members(
  `id` int NOT NULL auto_increment,
  `mixi_account_id` varchar(250) default NULL,
  `thumnail_url` longtext,
  `name` longtext,
  `mail` longtext,
  `password` longtext,
  `lv` int default NULL,
   exp int default null,
   insert_date date,
   update_date date,
  PRIMARY KEY (`id`)
)TYPE=InnoDB AUTO_INCREMENT=1;

create table photos(
id int not null auto_increment,
member_id int,
name varchar(250),
average_point int,
valid_flag int,
insert_date date,
primary key(`id`)
)TYPE=InnoDB AUTO_INCREMENT=1;

create table comments(
id int not null auto_increment,
photo_id int,
member_id int,
point int,
insert_date date,
primary key(`id`)
)TYPE=InnoDB AUTO_INCREMENT=1;