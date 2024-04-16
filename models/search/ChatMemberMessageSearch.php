<?php

namespace app\models\search;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form;
use yii\data\ActiveDataProvider;
use app\models\ChatMemberMessage;

class ChatMemberMessageSearch extends Form
{
	public $id;
	public $to_chat_member_id;
	public $from_chat_member_id;
	public $message;
	public $created_at;
	public $updated_at;
	
    public function rules(): array
    {
        return [
            [['id', 'to_chat_member_id', 'from_chat_member_id'], 'integer'],
            [['message', 'created_at', 'updated_at'], 'safe'],
        ];
    }

	/**
	 * @throws ValidateException
	 */
    public function search(array $params): ActiveDataProvider
    {
        $query = ChatMemberMessage::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		$this->validateOrThrow();

        $query->andFilterWhere([
            'id' => $this->id,
            'to_chat_member_id' => $this->to_chat_member_id,
            'from_chat_member_id' => $this->from_chat_member_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
