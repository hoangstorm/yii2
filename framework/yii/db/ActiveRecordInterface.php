<?php
/**
 * 
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace yii\db;

/**
 * ActiveRecordInterface
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
interface ActiveRecordInterface
{
	/**
	 * Returns the primary key name(s) for this AR class.
	 *
	 * Note that an array should be returned even when the record only has a single primary key.
	 *
	 * @return string[] the primary key name(s) for this AR class.
	 */
	public static function primaryKey();

	/**
	 * Returns the list of all attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributes();

	/**
	 * Returns the named attribute value.
	 * If this record is the result of a query and the attribute is not loaded,
	 * null will be returned.
	 * @param string $name the attribute name
	 * @return mixed the attribute value. Null if the attribute is not set or does not exist.
	 * @see hasAttribute()
	 */
	public function getAttribute($name);

	/**
	 * Sets the named attribute value.
	 * @param string $name the attribute name.
	 * @param mixed $value the attribute value.
	 * @see hasAttribute()
	 */
	public function setAttribute($name, $value);

	/**
	 * Returns a value indicating whether the model has an attribute with the specified name.
	 * @param string $name the name of the attribute
	 * @return boolean whether the model has an attribute with the specified name.
	 */
	public function hasAttribute($name);

	/**
	 * Returns the primary key value(s).
	 * @param boolean $asArray whether to return the primary key value as an array. If true,
	 * the return value will be an array with attribute names as keys and attribute values as values.
	 * Note that for composite primary keys, an array will always be returned regardless of this parameter value.
	 * @return mixed the primary key value. An array (attribute name => attribute value) is returned if the primary key
	 * is composite or `$asArray` is true. A string is returned otherwise (null will be returned if
	 * the key value is null).
	 */
	public function getPrimaryKey($asArray = false);

	/**
	 * Creates an [[ActiveQuery]] instance for query purpose.
	 *
	 * @param mixed $q the query parameter. This can be one of the followings:
	 *
	 *  - a scalar value (integer or string): query by a single primary key value and return the
	 *    corresponding record.
	 *  - an array of name-value pairs: query by a set of attribute values and return a single record matching all of them.
	 *  - null (not specified): return a new [[ActiveQuery]] object for further query purpose.
	 *
	 * @return ActiveQuery|ActiveRecord|null When `$q` is null, a new [[ActiveQuery]] instance
	 * is returned; when `$q` is a scalar or an array, an ActiveRecord object matching it will be
	 * returned (null will be returned if there is no matching).
	 */
	public static function find($q = null);

	/**
	 * Updates all records using the provided attribute values and conditions.
	 * For example, to change the status to be 1 for all customers whose status is 2:
	 *
	 * ~~~
	 * Customer::updateAll(['status' => 1], ['status' => '2']);
	 * ~~~
	 *
	 * @param array $attributes attribute values (name-value pairs) to be saved into the table
	 * @param array $condition the condition that matches the records that should get updated.
	 * Please refer to [[QueryInterface::where()]] on how to specify this parameter.
	 * @return integer the number of rows updated
	 */
	public static function updateAll($attributes, $condition);

	/**
	 * Deletes rows in the table using the provided conditions.
	 * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
	 *
	 * For example, to delete all customers whose status is 3:
	 *
	 * ~~~
	 * Customer::deleteAll('status = 3');
	 * ~~~
	 *
	 * @param string|array $condition the conditions that will be put in the WHERE part of the DELETE SQL.
	 * Please refer to [[Query::where()]] on how to specify this parameter.
	 * @return integer the number of rows deleted
	 */
	public static function deleteAll($condition);

	/**
	 * Saves the current record.
	 *
	 * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
	 * when [[isNewRecord]] is false.
	 *
	 * For example, to save a customer record:
	 *
	 * ~~~
	 * $customer = new Customer;  // or $customer = Customer::find($id);
	 * $customer->name = $name;
	 * $customer->email = $email;
	 * $customer->save();
	 * ~~~
	 *
	 *
	 * @param boolean $runValidation whether to perform validation before saving the record.
	 * If the validation fails, the record will not be saved to database.
	 * @return boolean whether the saving succeeds
	 */
	public function save($runValidation = true);

	/**
	 * Inserts a row into the associated database table using the attribute values of this record.
	 *
	 * This method performs the following steps in order:
	 *
	 * 1. call [[beforeValidate()]] when `$runValidation` is true. If validation
	 *    fails, it will skip the rest of the steps;
	 * 2. call [[afterValidate()]] when `$runValidation` is true.
	 * 3. call [[beforeSave()]]. If the method returns false, it will skip the
	 *    rest of the steps;
	 * 4. insert the record into database. If this fails, it will skip the rest of the steps;
	 * 5. call [[afterSave()]];
	 *
	 * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
	 * [[EVENT_BEFORE_INSERT]], [[EVENT_AFTER_INSERT]] and [[EVENT_AFTER_VALIDATE]]
	 * will be raised by the corresponding methods.
	 *
	 * Only the [[dirtyAttributes|changed attribute values]] will be inserted into database.
	 *
	 * If the table's primary key is auto-incremental and is null during insertion,
	 * it will be populated with the actual value after insertion.
	 *
	 * For example, to insert a customer record:
	 *
	 * ~~~
	 * $customer = new Customer;
	 * $customer->name = $name;
	 * $customer->email = $email;
	 * $customer->insert();
	 * ~~~
	 *
	 * @param boolean $runValidation whether to perform validation before saving the record.
	 * If the validation fails, the record will not be inserted into the database.
	 * @param array $attributes list of attributes that need to be saved. Defaults to null,
	 * meaning all attributes that are loaded from DB will be saved.
	 * @return boolean whether the attributes are valid and the record is inserted successfully.
	 * @throws \Exception in case insert failed.
	 */
	public function insert($runValidation = true);

	/**
	 * Saves the changes to this active record into the associated database table.
	 *
	 * This method performs the following steps in order:
	 *
	 * 1. call [[beforeValidate()]] when `$runValidation` is true. If validation
	 *    fails, it will skip the rest of the steps;
	 * 2. call [[afterValidate()]] when `$runValidation` is true.
	 * 3. call [[beforeSave()]]. If the method returns false, it will skip the
	 *    rest of the steps;
	 * 4. save the record into database. If this fails, it will skip the rest of the steps;
	 * 5. call [[afterSave()]];
	 *
	 * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
	 * [[EVENT_BEFORE_UPDATE]], [[EVENT_AFTER_UPDATE]] and [[EVENT_AFTER_VALIDATE]]
	 * will be raised by the corresponding methods.
	 *
	 * Only the [[changedAttributes|changed attribute values]] will be saved into database.
	 *
	 * For example, to update a customer record:
	 *
	 * ~~~
	 * $customer = Customer::find($id);
	 * $customer->name = $name;
	 * $customer->email = $email;
	 * $customer->update();
	 * ~~~
	 *
	 * Note that it is possible the update does not affect any row in the table.
	 * In this case, this method will return 0. For this reason, you should use the following
	 * code to check if update() is successful or not:
	 *
	 * ~~~
	 * if ($this->update() !== false) {
	 *     // update successful
	 * } else {
	 *     // update failed
	 * }
	 * ~~~
	 *
	 * @param boolean $runValidation whether to perform validation before saving the record.
	 * If the validation fails, the record will not be inserted into the database.
	 * @param array $attributes list of attributes that need to be saved. Defaults to null,
	 * meaning all attributes that are loaded from DB will be saved.
	 * @return integer|boolean the number of rows affected, or false if validation fails
	 * or [[beforeSave()]] stops the updating process.
	 * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
	 * being updated is outdated.
	 * @throws \Exception in case update failed.
	 */
	public function update($runValidation = true);

	/**
	 * Deletes the table row corresponding to this active record.
	 *
	 * This method performs the following steps in order:
	 *
	 * 1. call [[beforeDelete()]]. If the method returns false, it will skip the
	 *    rest of the steps;
	 * 2. delete the record from the database;
	 * 3. call [[afterDelete()]].
	 *
	 * In the above step 1 and 3, events named [[EVENT_BEFORE_DELETE]] and [[EVENT_AFTER_DELETE]]
	 * will be raised by the corresponding methods.
	 *
	 * @return integer|boolean the number of rows deleted, or false if the deletion is unsuccessful for some reason.
	 * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
	 * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
	 * being deleted is outdated.
	 * @throws \Exception in case delete failed.
	 */
	public function delete();

	/**
	 * Returns a value indicating whether the current record is new.
	 * @return boolean whether the record is new and should be inserted when calling [[save()]].
	 */
	public function getIsNewRecord();

	/**
	 * Returns a value indicating whether the given active record is the same as the current one.
	 * The comparison is made by comparing the table names and the primary key values of the two active records.
	 * If one of the records [[isNewRecord|is new]] they are also considered not equal.
	 * @param ActiveRecord $record record to compare to
	 * @return boolean whether the two active records refer to the same row in the same database table.
	 */
	public function equals($record);

	/**
	 * Creates an [[ActiveRelation]] instance.
	 * This method is called by [[hasOne()]] and [[hasMany()]] to create a relation instance.
	 * You may override this method to return a customized relation.
	 * @param array $config the configuration passed to the ActiveRelation class.
	 * @return ActiveRelation the newly created [[ActiveRelation]] instance.
	 */
	public static function createActiveRelation($config = []);

	/**
	 * Returns the relation object with the specified name.
	 * A relation is defined by a getter method which returns an [[ActiveRelation]] object.
	 * It can be declared in either the Active Record class itself or one of its behaviors.
	 * @param string $name the relation name
	 * @return ActiveRelation the relation object
	 */
	public function getRelation($name);

	/**
	 * Establishes the relationship between two models.
	 *
	 * The relationship is established by setting the foreign key value(s) in one model
	 * to be the corresponding primary key value(s) in the other model.
	 * The model with the foreign key will be saved into database without performing validation.
	 *
	 * If the relationship involves a pivot table, a new row will be inserted into the
	 * pivot table which contains the primary key values from both models.
	 *
	 * Note that this method requires that the primary key value is not null.
	 *
	 * @param string $name the case sensitive name of the relationship
	 * @param ActiveRecord $model the model to be linked with the current one.
	 * @param array $extraColumns additional column values to be saved into the pivot table.
	 * This parameter is only meaningful for a relationship involving a pivot table
	 * (i.e., a relation set with `[[ActiveRelation::via()]]` or `[[ActiveRelation::viaTable()]]`.)
	 * @throws InvalidCallException if the method is unable to link two models.
	 */
	public function link($name, $model, $extraColumns = []);

	/**
	 * Destroys the relationship between two models.
	 *
	 * The model with the foreign key of the relationship will be deleted if `$delete` is true.
	 * Otherwise, the foreign key will be set null and the model will be saved without validation.
	 *
	 * @param string $name the case sensitive name of the relationship.
	 * @param ActiveRecord $model the model to be unlinked from the current one.
	 * @param boolean $delete whether to delete the model that contains the foreign key.
	 * If false, the model's foreign key will be set null and saved.
	 * If true, the model containing the foreign key will be deleted.
	 * @throws InvalidCallException if the models cannot be unlinked
	 */
	public function unlink($name, $model, $delete = false);
}