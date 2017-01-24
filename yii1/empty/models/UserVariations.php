<?php

/**
 * This is the model class for table "{{user_variations}}".
 *
 * The followings are the available columns in table '{{user_variations}}':
 * @property string $id
 * @property string $id_template
 * @property string $id_variation
 * @property string $data
 * @property string $color_data
 * @property string $font_data
 * @property string $js_data
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $user_analytics_code
 * @property string $user_head_code
 * @property string $target_url
 * @property string $target_page
 * @property string $exit_popup
 * @property string $exit_popup_message
 * @property integer $exit_popup_redirect
 * @property string $exit_popup_redirect_url
 * @property string $form
 * @property string $rand_hash
 * @property string $service_integration
 * @property string $service_list
 * @property string $sales_path
 * @property integer $industry
 * @property string $screenshot
 *
 * The followings are the available model relations:
 * @property Templates $idTemplate
 * @property Variations $idVariation
 */
class UserVariations extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_variations}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_template, id_variation', 'required'),
			array('exit_popup_redirect, industry', 'numerical', 'integerOnly'=>true),
			array('id_template, id_variation', 'length', 'max'=>10),
			array('title, description, keywords, target_url, target_page, exit_popup_redirect_url, rand_hash, service_integration, service_list, sales_path, screenshot', 'length', 'max'=>255),
			array('data, color_data, font_data, js_data, user_analytics_code, user_head_code, exit_popup, exit_popup_message, form', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_template, id_variation, data, color_data, font_data, js_data, title, description, keywords, user_analytics_code, user_head_code, target_url, target_page, exit_popup, exit_popup_message, exit_popup_redirect, exit_popup_redirect_url, form, rand_hash, service_integration, service_list, sales_path, industry, screenshot', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'idTemplate' => array(self::BELONGS_TO, 'Templates', 'id_template'),
			'idVariation' => array(self::BELONGS_TO, 'Variations', 'id_variation'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_template' => 'link table templates',
			'id_variation' => 'link table variations',
			'data' => 'this is data',
			'color_data' => 'this is color_data',
			'font_data' => 'this is font_data',
			'js_data' => 'this is js_data',
			'title' => 'for SEO, this is page_title',
			'description' => 'for SEO, this is page_description',
			'keywords' => 'for SEO, this is page_keywords',
			'user_analytics_code' => 'User Analytics Code',
			'user_head_code' => 'User Head Code',
			'target_url' => 'Target Url',
			'target_page' => 'Target Page',
			'exit_popup' => 'Exit Popup',
			'exit_popup_message' => 'Exit Popup Message',
			'exit_popup_redirect' => 'Exit Popup Redirect',
			'exit_popup_redirect_url' => 'Exit Popup Redirect Url',
			'form' => 'Form',
			'rand_hash' => 'Rand Hash',
			'service_integration' => 'Service Integration',
			'service_list' => 'Service List',
			'sales_path' => 'Sales Path',
			'industry' => 'Industry',
			'screenshot' => 'Screenshot',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('id_template',$this->id_template,true);
		$criteria->compare('id_variation',$this->id_variation,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('color_data',$this->color_data,true);
		$criteria->compare('font_data',$this->font_data,true);
		$criteria->compare('js_data',$this->js_data,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('user_analytics_code',$this->user_analytics_code,true);
		$criteria->compare('user_head_code',$this->user_head_code,true);
		$criteria->compare('target_url',$this->target_url,true);
		$criteria->compare('target_page',$this->target_page,true);
		$criteria->compare('exit_popup',$this->exit_popup,true);
		$criteria->compare('exit_popup_message',$this->exit_popup_message,true);
		$criteria->compare('exit_popup_redirect',$this->exit_popup_redirect);
		$criteria->compare('exit_popup_redirect_url',$this->exit_popup_redirect_url,true);
		$criteria->compare('form',$this->form,true);
		$criteria->compare('rand_hash',$this->rand_hash,true);
		$criteria->compare('service_integration',$this->service_integration,true);
		$criteria->compare('service_list',$this->service_list,true);
		$criteria->compare('sales_path',$this->sales_path,true);
		$criteria->compare('industry',$this->industry);
		$criteria->compare('screenshot',$this->screenshot,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserVariations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
