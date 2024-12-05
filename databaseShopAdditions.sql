show databases;
use tspgamblesmart;

drop table if exists userProfile;
drop table if exists ownedSuburbs;
drop table if exists ownedFarms;
drop table if exists ownedForests;
drop table if exists ownedOceans;
drop table if exists ownedDeserts;
drop table if exists ownedOthers;

create table if not exists userProfile (
`account` integer,
`equipped` varchar(20),
primary key(`account`),
foreign key(`account`) references users(`account`)
);

create table if not exists ownedSuburbs (
    `account` integer,
    `cost` integer,
    `turtle` bool not null default false,
    `cat` bool not null default false,
    `dog` bool not null default false,
    `hamster` bool not null default false,
    `mouse` bool not null default false,
    primary key(`account`),
    foreign key(`account`) references users(`account`)
);

create table if not exists ownedFarms (
    `account` integer, 
    `cost` integer,
    `cow` bool not null default false,
    `sheep` bool not null default false,
    `rooster` bool not null default false,
    `chick` bool not null default false,
    `pig` bool not null default false,
    primary key(`account`), 
    foreign key(`account`) references users(`account`)
);

create table if not exists ownedForests (
    `account` integer,
    `cost` integer,
    `chipmunk` bool not null default false,
    `rabbit` bool not null default false,
    `bear` bool not null default false,
    `wolf` bool not null default false,
    `fox` bool not null default false,
    `deer` bool not null default false,
    primary key(`account`),
    foreign key(`account`) references users(`account`)
);

create table if not exists ownedOceans (
    `account` integer,
    `cost` integer,
    `octopus` bool not null default false,
    `fish` bool not null default false,
    `puffer` bool not null default false,
    `dolphin` bool not null default false,
    `whale` bool not null default false,
    primary key(`account`),
    foreign key(`account`) references users(`account`)
);


create table if not exists ownedDeserts (
    `account` integer,
    `cost` integer,
    `camel` bool not null default false,
    `elephant` bool not null default false,
    `lizard` bool not null default false,
    `lion` bool not null default false,
    `rhino` bool not null default false,
    primary key(`account`),
    foreign key(`account`) references users(`account`)
);

create table if not exists ownedOthers (
    `account` integer,
    `cost` integer,
    `dodo` bool not null default false,
    `trex` bool not null default false,
    `dragon` bool not null default false,
    `dino` bool not null default false,
    `unicorn` bool not null default false,
    primary key(`account`),
    foreign key(`account`) references users(`account`)
);
show tables;

delimiter //
drop procedure if exists create_user_profile//
create procedure create_user_profile(account integer)
begin
insert into userProfile values(account, "\\u{1F928}");
insert into ownedSuburbs values(account, 50, false, false, false, false, false);
insert into ownedFarms values(account, 100, false, false, false, false, false);
insert into ownedForests values(account, 200, false, false, false, false, false, false);
insert into ownedOceans values(account, 300, false, false, false, false, false);
insert into ownedDeserts values(account, 400, false, false, false, false, false);
insert into ownedOthers values(account, 500, false, false, false, false, false);
end//

delimiter ;

show tables;

select * from users;
select * from userdata;
select * from balancehistory;
select * from tasks;