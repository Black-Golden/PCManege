<?php  $rows=array (
  0 => 
  array (
    'type' => 'hidden',
    'title' => '通知ID',
    'field' => 'id',
    'field_type' => 'int',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入通知ID',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入通知ID',
    ),
  ),
  1 => 
  array (
    'type' => 'input',
    'title' => '通知标题',
    'field' => 'title',
    'field_type' => 'varchar',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入通知标题',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入通知标题',
    ),
  ),
  2 => 
  array (
    'type' => 'input',
    'title' => '通知内容',
    'field' => 'content',
    'field_type' => 'text',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入通知内容',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入通知内容',
    ),
  ),
  3 => 
  array (
    'type' => 'radio',
    'title' => '是否置顶',
    'field' => 'is_top',
    'field_type' => 'tinyint',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请选择是否置顶',
    ),
    'options' => 
    array (
      0 => 
      array (
        'value' => 1,
        'label' => '是',
      ),
      1 => 
      array (
        'value' => 2,
        'label' => '否',
      ),
    ),
  ),
  4 => 
  array (
    'type' => 'input',
    'title' => '阅读量',
    'field' => 'browse',
    'field_type' => 'int',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入阅读量',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入阅读量',
    ),
  ),
  5 => 
  array (
    'type' => 'radio',
    'title' => '状态',
    'field' => 'status',
    'field_type' => 'tinyint',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请选择状态',
    ),
    'options' => 
    array (
      0 => 
      array (
        'value' => 1,
        'label' => '已发布',
      ),
      1 => 
      array (
        'value' => 2,
        'label' => '待发布',
      ),
    ),
  ),
  6 => 
  array (
    'type' => 'input',
    'title' => '添加人',
    'field' => 'create_user',
    'field_type' => 'int',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入添加人',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入添加人',
    ),
  ),
  7 => 
  array (
    'type' => 'input',
    'title' => '更新人',
    'field' => 'update_user',
    'field_type' => 'int',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入更新人',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入更新人',
    ),
  ),
  8 => 
  array (
    'type' => 'input',
    'title' => '',
    'field' => 'by_id',
    'field_type' => 'int',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入',
    ),
  ),
  9 => 
  array (
    'type' => 'input',
    'title' => '1官方文档 2使用教程',
    'field' => 'type',
    'field_type' => 'int',
    'props' => 
    array (
      'multiple' => 'false',
      'placeholder' => '请输入1官方文档 2使用教程',
    ),
    'validate' => 
    array (
      'type' => 'string',
      'required' => 'true',
      'message' => '请输入1官方文档 2使用教程',
    ),
  ),
); return $rows;