<?php 
return array (
  'admin_node' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'lenght' => '8',
      'unsigned' => true,
      'null' => false,
    ),
    'controller' => 
    array (
      'field' => 'controller',
      'key' => '',
      'default' => '',
      'lenght' => '80',
      'unsigned' => false,
      'null' => true,
    ),
    'action' => 
    array (
      'field' => 'action',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => '',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'parent_id' => 
    array (
      'field' => 'parent_id',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'url_host' => 
    array (
      'field' => 'url_host',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'int',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'admin_role' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'lenght' => '8',
      'unsigned' => true,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => '',
      'default' => '',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '200',
      'unsigned' => false,
      'null' => true,
    ),
    'rule' => 
    array (
      'field' => 'rule',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '600',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'admin_user' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'UNI',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'password' => 
    array (
      'field' => 'password',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => NULL,
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'is_form' => 
    array (
      'field' => 'is_form',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'role_id' => 
    array (
      'field' => 'role_id',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '6',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'last_time' => 
    array (
      'field' => 'last_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'last_ip' => 
    array (
      'field' => 'last_ip',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'admin_user_role' => 
  array (
    'role_id' => 
    array (
      'field' => 'role_id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'select_type' => 
    array (
      'field' => 'select_type',
      'key' => '',
      'default' => '2',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'role_id',
  ),
  'auth_account_ratethrottle' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'UNI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'trade_pwd_failed_count' => 
    array (
      'field' => 'trade_pwd_failed_count',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => false,
    ),
    'trade_pwd_last_failed_time' => 
    array (
      'field' => 'trade_pwd_last_failed_time',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'login_failed_count' => 
    array (
      'field' => 'login_failed_count',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => false,
    ),
    'login_last_failed_time' => 
    array (
      'field' => 'login_last_failed_time',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'identify_failed_count' => 
    array (
      'field' => 'identify_failed_count',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => false,
    ),
    'identify_last_failed_time' => 
    array (
      'field' => 'identify_last_failed_time',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'auth_bank_bind_request' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'requestid' => 
    array (
      'field' => 'requestid',
      'key' => 'UNI',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'idcardno' => 
    array (
      'field' => 'idcardno',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'bankcode' => 
    array (
      'field' => 'bankcode',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'bankname' => 
    array (
      'field' => 'bankname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => true,
    ),
    'channel' => 
    array (
      'field' => 'channel',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'cardno' => 
    array (
      'field' => 'cardno',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'userip' => 
    array (
      'field' => 'userip',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'auth_bank_card' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'bankcode' => 
    array (
      'field' => 'bankcode',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'bankname' => 
    array (
      'field' => 'bankname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => true,
    ),
    'channel' => 
    array (
      'field' => 'channel',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'realname' => 
    array (
      'field' => 'realname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => true,
    ),
    'cardno' => 
    array (
      'field' => 'cardno',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'from_platform' => 
    array (
      'field' => 'from_platform',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'auth_captcha_ratethrottle' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'mobile' => 
    array (
      'field' => 'mobile',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '15',
      'unsigned' => false,
      'null' => true,
    ),
    'is_sms' => 
    array (
      'field' => 'is_sms',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => true,
    ),
    'counter' => 
    array (
      'field' => 'counter',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'not_valid' => 
    array (
      'field' => 'not_valid',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'last_time' => 
    array (
      'field' => 'last_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'auth_identify' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => false,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'is_valid' => 
    array (
      'field' => 'is_valid',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'msg' => 
    array (
      'field' => 'msg',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'xp' => 
    array (
      'field' => 'xp',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'province' => 
    array (
      'field' => 'province',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'is_manual' => 
    array (
      'field' => 'is_manual',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'from_client' => 
    array (
      'field' => 'from_client',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'auth_ip_ratethrottle' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'ip' => 
    array (
      'field' => 'ip',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '24',
      'unsigned' => false,
      'null' => false,
    ),
    'handle' => 
    array (
      'field' => 'handle',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '15',
      'unsigned' => false,
      'null' => false,
    ),
    'counter' => 
    array (
      'field' => 'counter',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'last_time' => 
    array (
      'field' => 'last_time',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'auth_user' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'username' => 
    array (
      'field' => 'username',
      'key' => 'UNI',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'password' => 
    array (
      'field' => 'password',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '128',
      'unsigned' => false,
      'null' => false,
    ),
    'trade_pwd' => 
    array (
      'field' => 'trade_pwd',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '128',
      'unsigned' => false,
      'null' => true,
    ),
    'first_name' => 
    array (
      'field' => 'first_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'last_name' => 
    array (
      'field' => 'last_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'realname' => 
    array (
      'field' => 'realname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => true,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'display_name' => 
    array (
      'field' => 'display_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'gender' => 
    array (
      'field' => 'gender',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'birthday' => 
    array (
      'field' => 'birthday',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'email' => 
    array (
      'field' => 'email',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '75',
      'unsigned' => false,
      'null' => true,
    ),
    'from_user_id' => 
    array (
      'field' => 'from_user_id',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'invite_code' => 
    array (
      'field' => 'invite_code',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'from_channel' => 
    array (
      'field' => 'from_channel',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'from_platform' => 
    array (
      'field' => 'from_platform',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'system' => 
    array (
      'field' => 'system',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'last_login' => 
    array (
      'field' => 'last_login',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'last_ip' => 
    array (
      'field' => 'last_ip',
      'key' => '',
      'default' => 'unkown',
      'type' => 'varchar',
      'lenght' => '15',
      'unsigned' => false,
      'null' => true,
    ),
    'ip_area' => 
    array (
      'field' => 'ip_area',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'is_active' => 
    array (
      'field' => 'is_active',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'is_company' => 
    array (
      'field' => 'is_company',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'level' => 
    array (
      'field' => 'level',
      'key' => '',
      'default' => '-1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'score' => 
    array (
      'field' => 'score',
      'key' => '',
      'default' => '0',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'award_experience' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '64',
      'unsigned' => false,
      'null' => false,
    ),
    'amount_type' => 
    array (
      'field' => 'amount_type',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => '0',
      'unsigned' => true,
      'null' => false,
    ),
    'min_amount' => 
    array (
      'field' => 'min_amount',
      'key' => '',
      'default' => '0',
      'unsigned' => true,
      'null' => false,
    ),
    'max_amount' => 
    array (
      'field' => 'max_amount',
      'key' => '',
      'default' => '0',
      'unsigned' => true,
      'null' => false,
    ),
    'days' => 
    array (
      'field' => 'days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'limit_node' => 
    array (
      'field' => 'limit_node',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'award_extend' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'user' => 
    array (
      'field' => 'user',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'award_type' => 
    array (
      'field' => 'award_type',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => true,
      'null' => false,
    ),
    'award_id' => 
    array (
      'field' => 'award_id',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => true,
    ),
    'send_count' => 
    array (
      'field' => 'send_count',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'send_status' => 
    array (
      'field' => 'send_status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'award_interestcoupon' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'rate' => 
    array (
      'field' => 'rate',
      'key' => '',
      'default' => '0.0000',
      'unsigned' => true,
      'null' => true,
    ),
    'days' => 
    array (
      'field' => 'days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'limit_node' => 
    array (
      'field' => 'limit_node',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'award_node' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'config_capital' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => false,
    ),
    'config_value' => 
    array (
      'field' => 'config_value',
      'key' => '',
      'default' => '0.00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'config_purchase' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => '0.00',
      'unsigned' => false,
      'null' => true,
    ),
    'purchase_amount' => 
    array (
      'field' => 'purchase_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'margin_margin' => 
  array (
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'avaliable_amount' => 
    array (
      'field' => 'avaliable_amount',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => false,
      'null' => true,
    ),
    'principal_amount' => 
    array (
      'field' => 'principal_amount',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => false,
      'null' => true,
    ),
    'withdrawing_amount' => 
    array (
      'field' => 'withdrawing_amount',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => false,
      'null' => true,
    ),
    'invset_amount' => 
    array (
      'field' => 'invset_amount',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'user_id',
  ),
  'margin_recharge' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'order_id' => 
    array (
      'field' => 'order_id',
      'key' => 'UNI',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'realname' => 
    array (
      'field' => 'realname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '18',
      'unsigned' => false,
      'null' => true,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => true,
      'null' => false,
    ),
    'pay_channel' => 
    array (
      'field' => 'pay_channel',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'bank_account' => 
    array (
      'field' => 'bank_account',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'bank_name' => 
    array (
      'field' => 'bank_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'is_cash_buy' => 
    array (
      'field' => 'is_cash_buy',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'is_normal' => 
    array (
      'field' => 'is_normal',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => true,
    ),
    'from_platform' => 
    array (
      'field' => 'from_platform',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'error_message' => 
    array (
      'field' => 'error_message',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '100',
      'lenght' => '4',
      'unsigned' => false,
      'null' => true,
    ),
    'remake' => 
    array (
      'field' => 'remake',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'client_ip' => 
    array (
      'field' => 'client_ip',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '15',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'margin_record' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => '',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'record_id' => 
    array (
      'field' => 'record_id',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'type' => 
    array (
      'field' => 'type',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => true,
    ),
    'type_to_cn' => 
    array (
      'field' => 'type_to_cn',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'before_avaliable_amount' => 
    array (
      'field' => 'before_avaliable_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'after_avaliable_amount' => 
    array (
      'field' => 'after_avaliable_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'is_affected_amount' => 
    array (
      'field' => 'is_affected_amount',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '200',
      'lenght' => '5',
      'unsigned' => true,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'margin_refund' => 
  array (
    'refund_id' => 
    array (
      'field' => 'refund_id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => true,
      'null' => true,
    ),
    'interest' => 
    array (
      'field' => 'interest',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => true,
      'null' => true,
    ),
    'increase' => 
    array (
      'field' => 'increase',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => true,
      'null' => true,
    ),
    'exp_interest' => 
    array (
      'field' => 'exp_interest',
      'key' => '',
      'default' => '0.0000000000',
      'unsigned' => true,
      'null' => true,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => NULL,
      'lenght' => '6',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'refund_id',
  ),
  'margin_refund_counter' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'date' => 
    array (
      'field' => 'date',
      'key' => 'MUL',
      'default' => NULL,
      'lenght' => '7',
      'unsigned' => false,
      'null' => false,
    ),
    'counter' => 
    array (
      'field' => 'counter',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'margin_withdraw' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'realname' => 
    array (
      'field' => 'realname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '18',
      'unsigned' => false,
      'null' => true,
    ),
    'order_id' => 
    array (
      'field' => 'order_id',
      'key' => 'UNI',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'source_amount' => 
    array (
      'field' => 'source_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => true,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => true,
      'null' => false,
    ),
    'fee' => 
    array (
      'field' => 'fee',
      'key' => '',
      'default' => '0.00',
      'unsigned' => true,
      'null' => true,
    ),
    'pay_channel' => 
    array (
      'field' => 'pay_channel',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'bank_account' => 
    array (
      'field' => 'bank_account',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'bank_code' => 
    array (
      'field' => 'bank_code',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'bank_name' => 
    array (
      'field' => 'bank_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'from_platform' => 
    array (
      'field' => 'from_platform',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'error_message' => 
    array (
      'field' => 'error_message',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'is_manual' => 
    array (
      'field' => 'is_manual',
      'key' => 'MUL',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '100',
      'lenght' => '4',
      'unsigned' => false,
      'null' => true,
    ),
    'real_status' => 
    array (
      'field' => 'real_status',
      'key' => '',
      'default' => '100',
      'lenght' => '4',
      'unsigned' => true,
      'null' => true,
    ),
    'remake' => 
    array (
      'field' => 'remake',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'client_ip' => 
    array (
      'field' => 'client_ip',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '15',
      'unsigned' => false,
      'null' => false,
    ),
    'refund_status' => 
    array (
      'field' => 'refund_status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_activity' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'img_url' => 
    array (
      'field' => 'img_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'link_url' => 
    array (
      'field' => 'link_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'sort' => 
    array (
      'field' => 'sort',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'desc' => 
    array (
      'field' => 'desc',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_article' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'img_url' => 
    array (
      'field' => 'img_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'cate_node' => 
    array (
      'field' => 'cate_node',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'content' => 
    array (
      'field' => 'content',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'sort' => 
    array (
      'field' => 'sort',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_article_node' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_banner' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'img_url' => 
    array (
      'field' => 'img_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'link_url' => 
    array (
      'field' => 'link_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'pos' => 
    array (
      'field' => 'pos',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'sort' => 
    array (
      'field' => 'sort',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_checkin' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'continue_days' => 
    array (
      'field' => 'continue_days',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_experience' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'source_id' => 
    array (
      'field' => 'source_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'source_name' => 
    array (
      'field' => 'source_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => true,
      'null' => false,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'continuous_days' => 
    array (
      'field' => 'continuous_days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'is_use' => 
    array (
      'field' => 'is_use',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_interestcoupon' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'source_id' => 
    array (
      'field' => 'source_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'source_name' => 
    array (
      'field' => 'source_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'rate' => 
    array (
      'field' => 'rate',
      'key' => '',
      'default' => '0.0000',
      'unsigned' => true,
      'null' => true,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'continuous_days' => 
    array (
      'field' => 'continuous_days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'is_use' => 
    array (
      'field' => 'is_use',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'sms_log' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'mobile' => 
    array (
      'field' => 'mobile',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'contents' => 
    array (
      'field' => 'contents',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'created_at' => 
    array (
      'field' => 'created_at',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'sms_template' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'node_name' => 
    array (
      'field' => 'node_name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'content_tpl' => 
    array (
      'field' => 'content_tpl',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '300',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => 'CURRENT_TIMESTAMP',
      'unsigned' => false,
      'null' => true,
    ),
    'des' => 
    array (
      'field' => 'des',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '200',
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'user_award_counts' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'award_type' => 
    array (
      'field' => 'award_type',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '25',
      'unsigned' => false,
      'null' => true,
    ),
    'award_nums' => 
    array (
      'field' => 'award_nums',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '8',
      'unsigned' => true,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
);