<?php
/**
 * Contains the UserSearch model for table "usermanager_user".
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use nickcv\usermanager\models\User;
use nickcv\usermanager\helpers\AuthHelper;

/**
 * UserSearch represents the model behind the search form about `nickcv\usermanager\models\User`.
 */
class UserSearch extends User
{
    public $role;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['email', 'role', 'password', 'firstname', 'lastname', 'authkey', 'token', 'registration_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'registration_date' => $this->registration_date,
        ]);
        
        $validIDs = [];
        if ($this->role) {
            $validIDs = AuthHelper::getUsersWithRole($this->role) ? AuthHelper::getUsersWithRole($this->role) : 0;
        }

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'authkey', $this->authkey])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['id' => $validIDs]);

        return $dataProvider;
    }
}
