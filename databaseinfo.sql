show databases;
create database if not exists tspgamblesmart;
use tspgamblesmart;

drop table if exists tasks;
drop table if exists balancehistory;
drop table if exists userdata;
drop table if exists users;

create table if not exists users (
`account` integer not null auto_increment, 
`name` varchar(15), 
`password` char(64),  
primary key(`account`)
);

create table if not exists userdata (
`account` integer, 
`balance` integer, 
`blackjack` integer, 
`poker` integer, 
`slots` integer, 
primary key(`account`), 
foreign key(`account`) references users(`account`)
);

create table if not exists balancehistory (
`account` integer, 
`id` integer not null auto_increment,  
`oldValue` integer, 
`newValue` integer,
primary key(`id`, `account`), 
foreign key(`account`) references users(`account`)
);

create table if not exists tasks (
`account` integer, 
`id` integer not null auto_increment, 
`task` varchar(64),
`value` integer,
`completed` bool not null default false, 
primary key(`id`, `account`), 
foreign key(`account`) references users(`account`)
);

show tables;

delimiter //
drop procedure if exists create_user//
create procedure create_user(name varchar(15), password char(64))
begin
insert into users values(null, name, (sha2(password, 256)));
end//

drop procedure if exists add_userdata//
create procedure add_userdata(account integer, balance integer, blackjack integer, poker integer, slots integer) 
begin
insert into userdata values(account, balance, blackjack, poker, slots);
end//

drop procedure if exists add_transaction//
create procedure add_transaction(account integer, oldValue integer, newValue integer)
begin
insert into balancehistory values(account, null, oldValue, newValue);
end//

drop procedure if exists add_task//
create procedure add_task(account integer, task varchar(64), value integer)
begin
insert into tasks values(account, null, task, value, false);
end// 

delimiter ;

show tables;

call create_user('admin', 'admin');
call create_user('admin2', 'admin2');

call add_userdata(1, 500, 0, 0, 0);

update userdata set balance=450 where account=1;

call add_transaction(1, 500, 450);

call add_task(1,'Do the dishes',10);

update tasks set completed=true where id=1;

select * from users;
select * from userdata;
select * from balancehistory;
select * from tasks;