CREATE TABLE  urls (
    url_id      number primary key,
    url_path    varchar,
    redirection varchar,
    domain_id   number, --foriegn key

    creator     number --username is bad :(,

    date_created
    date_last_accessed
);

CREATE TABLE  domains (
    domain_id   number primary key

    creator     number --username is bad :(,
);

CREATE TABLE generate_type (
    type_id
);

CREATE TABLE generate_type_to_domain (
    type_id
);

CREATE TABLE generate_type_to_url (
    type_id
);

CREATE TABLE redirection_type (
    type_id
);

CREATE TABLE redirection_type (
    type_id
-- 
);

CREATE TABLE  hostname (
    hostname_id number primary key,
    domain_id   number, --foriegn key
    hostname    varchar
);

CREATE TABLE permissions (
    permission_id
    permission_description
    permission_name
);
/*

*/

CREATE TABLE permissions_to_domains (
    permission_id
    group_name
    domain_id
);

CREATE TABLE permissions_to_urls (
    permission_id
    group_name
    url_id
);