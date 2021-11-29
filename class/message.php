<?php

use Imponeer\Database\Criteria\CriteriaCompo;
use Imponeer\Database\Criteria\CriteriaItem;
use ImpressCMS\Core\Models\AbstractExtendedHandler;
use ImpressCMS\Core\Models\AbstractExtendedModel;

/**
 * @property int|null message_id
 * @property string message_title
 * @property string message_content
 * @property int message_from_uid
 * @property bool message_show_on_sent
 * @property bool message_show_on_inbox
 * @property string message_status
 */
class ImmessageMessage extends AbstractExtendedModel
{

	/**
	 * Message is a draft
	 */
	public const _IMMESSAGE_STATUS_DRAFT = 0;

	/**
	 * Message was sent
	 */
	public const _IMMESSAGE_STATUS_SEND = 1;

	/**
	 * Message was read
	 */
	public const _IMMESSAGE_STATUS_READ = 2;

	/**
	 * Message is in trash
	 */
	public const _IMMESSAGE_STATUS_TRASH = 3;

	/**
	 * Message is deleted
	 */
	public const _IMMESSAGE_STATUS_DELETED = 4;

	public function __construct($handler)
	{
		parent::__construct($handler);

		$this->quickInitVar('message_id', XOBJ_DTYPE_INT, true, _CO_IMMESSAGE_MESSAGE_ID, _CO_IMMESSAGE_MESSAGE_ID_DSC);
		$this->quickInitVar('message_title', XOBJ_DTYPE_TXTBOX, false, _CO_IMMESSAGE_MESSAGE_TITLE, _CO_IMMESSAGE_MESSAGE_TITLE_DSC);
		$this->setFieldAsRequired('message_title');
		$this->quickInitVar('message_content', XOBJ_DTYPE_TXTAREA, false, _CO_IMMESSAGE_MESSAGE_CONTENT, _CO_IMMESSAGE_MESSAGE_CONTENT_DSC);
		$this->setFieldAsRequired('message_content');
		$this->quickInitVar('message_from_uid', XOBJ_DTYPE_INT, false, _CO_IMMESSAGE_MESSAGE_FROM_UID, _CO_IMMESSAGE_MESSAGE_FROM_UID_DSC, \icms::$user->uid);
		$this->hideFieldFromForm('message_from_uid');
		$this->quickInitVar('message_to_uid', XOBJ_DTYPE_INT, false, _CO_IMMESSAGE_MESSAGE_TO_UID, _CO_IMMESSAGE_MESSAGE_TO_UID_DSC);
		$this->quickInitVar('message_status', XOBJ_DTYPE_INT, false, _CO_IMMESSAGE_MESSAGE_STATUS, _CO_IMMESSAGE_MESSAGE_STATUS_DSC, self::_IMMESSAGE_STATUS_SEND);
		$this->quickInitVar('message_creation_date', XOBJ_DTYPE_LTIME, false, _CO_IMMESSAGE_MESSAGE_CREATION_DATE, _CO_IMMESSAGE_MESSAGE_CREATION_DATE_DSC, mktime());
		$this->hideFieldFromForm('message_creation_date');
		$this->quickInitVar('message_modification_date', XOBJ_DTYPE_LTIME, false, _CO_IMMESSAGE_MESSAGE_MODIFICATION_DATE, _CO_IMMESSAGE_MESSAGE_MODIFICATION_DATE_DSC, mktime());
		$this->hideFieldFromForm('message_modification_date');

		$this->quickInitVar('message_show_on_inbox', XOBJ_DTYPE_INT, false, false, false, true);
		$this->hideFieldFromForm('message_show_on_inbox');
		$this->quickInitVar('message_show_on_sent', XOBJ_DTYPE_INT, false, false, false, true);
		$this->hideFieldFromForm('message_show_on_sent');

		$this->initCommonVar('dohtml', false, true);
		$this->initCommonVar('dobr', false);
		$this->initCommonVar('doimage', false, true);
		$this->initCommonVar('dosmiley', false, true);
		$this->initCommonVar('doxcode', false, true);

		$this->setControl('message_to_uid', 'user');
		$this->setControl('message_content', 'dhtmltextarea');
		$this->setControl('message_status', array('itemHandler' => 'message',
			'method' => 'getMessageStatusArray',
			'module' => 'immessage'
		));

	}

	public function getVar($key, $format = 's')
	{
		if ($format === 's' && in_array($key, array('message_to_uid', 'message_from_uid', 'message_status'))) {
			return $this->$key();
		}
		return parent::getVar($key, $format);
	}

	private function message_to_uid()
	{
		$member_handler = \xoops_gethandler('user');
		$member = $member_handler->get($this->getVar('message_to_uid', 'e'));
		return $member->getVar('uname');
	}

	private function message_from_uid()
	{
		$member_handler = \xoops_gethandler('user');
		$member = $member_handler->get($this->getVar('message_from_uid', 'e'));
		return $member->getVar('uname');
	}

	private function message_status()
	{
		global $xoopsUser;
		switch ($this->getVar('message_status', 'e')) {
			case self::_IMMESSAGE_STATUS_SEND:
				if ((int)\icms::$user->getVar('uid') === (int)$this->getVar('message_from_uid', 'e')) {
					$ret = "<img src='" . ICMS_URL . "/images/crystal/actions/mail_send.png' alt='" . _CO_IMMESSAGE_MESSAGE_STATUS_SENT . "'/>";
				} else {
					$ret = "<img src='" . ICMS_URL . "/images/crystal/actions/mail_get.png' alt='" . _CO_IMMESSAGE_MESSAGE_STATUS_SENT . "'/>";
				}
				break;
			case self::_IMMESSAGE_STATUS_DRAFT:
				$ret = "<img src='" . ICMS_URL . "/images/crystal/actions/mail_new.png' alt='" . _CO_IMMESSAGE_MESSAGE_STATUS_DRAFT . "'/>";
				break;
			case self::_IMMESSAGE_STATUS_READ:
			default:
				$ret = "<img src='" . ICMS_URL . "/images/crystal/actions/mail_generic.png' alt='" . _CO_IMMESSAGE_MESSAGE_STATUS_READ . "'/>";
				break;
		}
		return $ret;
	}

	public function getMessageTrashButton()
	{
		$ret = "<a href='" . IMMESSAGE_URL . "/message.php?op=trash&message_id=" . $this->getVar('message_id') . "' title='" . _CO_IMMESSAGE_MESSAGE_SEND_TO_TRASH . "'>";
		$ret .= "<img src='" . ICMS_URL . "/images/crystal/actions/mail_delete.png' alt='" . _CO_IMMESSAGE_MESSAGE_SEND_TO_TRASH . "'/>";
		$ret .= "</a>";
		return $ret;
	}

	public function getMessageDeleteFromSentButton()
	{
		$ret = "<a href='" . IMMESSAGE_URL . "/message.php?op=dfsent&message_id=" . $this->getVar('message_id') . "' title='" . _CO_IMMESSAGE_MESSAGE_SEND_TO_TRASH . "'>";
		$ret .= "<img src='" . ICMS_URL . "/images/crystal/actions/mail_delete.png' alt='" . _CO_IMMESSAGE_MESSAGE_SEND_TO_TRASH . "'/>";
		$ret .= "</a>";
		return $ret;
	}

	public function getMessageDeleteFromTrashButton()
	{
		$ret = "<a href='" . IMMESSAGE_URL . "/message.php?op=delete&message_id=" . $this->getVar('message_id') . "' title='" . _CO_IMMESSAGE_MESSAGE_DELETE_FROM_TRASH . "'>";
		$ret .= "<img src='" . ICMS_URL . "/images/crystal/actions/mail_delete.png' alt='" . _CO_IMMESSAGE_MESSAGE_DELETE_FROM_TRASH . "'/>";
		$ret .= "</a>";
		return $ret;
	}

	public function getMessageResponseButton()
	{
		$ret = "<a href='" . IMMESSAGE_URL . "/message.php?op=response&message_id=" . $this->getVar('message_id') . "' title='" . _CO_IMMESSAGE_MESSAGE_SEND_RESPONSE . "'>";
		$ret .= "<img src='" . ICMS_URL . "/images/crystal/actions/mail_reply.png' alt='" . _CO_IMMESSAGE_MESSAGE_SEND_RESPONSE . "'/>";
		$ret .= "</a>";
		return $ret;
	}

	public function getMessageForwardButton()
	{
		$ret = "<a href='" . IMMESSAGE_URL . "/message.php?op=forward&message_id=" . $this->getVar('message_id') . "' title='" . _CO_IMMESSAGE_MESSAGE_SEND_FORWARD . "'>";
		$ret .= "<img src='" . ICMS_URL . "/images/crystal/actions/mail_forward.png' alt='" . _CO_IMMESSAGE_MESSAGE_SEND_FORWARD . "'/>";
		$ret .= "</a>";
		return $ret;
	}

	public function getButtons()
	{
		$ret = "";
		$ret .= $this->getMessageResponseButton();
		$ret .= "&nbsp;&nbsp;";
		$ret .= $this->getMessageForwardButton();
		$ret .= "&nbsp;&nbsp;";
		$ret .= $this->getMessageTrashButton();
		return $ret;
	}

}

class ImmessageMessageHandler extends AbstractExtendedHandler
{

	public function __construct($db)
	{
		parent::__construct($db, 'message', 'message_id', 'message_title', 'message_content', 'immessage');
	}

	public function getMessageStatusArray()
	{
		if (isset($_GET['op'])) $op = $_GET['op'];
		if (isset($_POST['op'])) $op = $_POST['op'];
		$options = array('mod', 'response', 'forward');
		$ret[\ImmessageMessage::_IMMESSAGE_STATUS_DRAFT] = _CO_IMMESSAGE_MESSAGE_STATUS_DRAFT;
		$ret[\ImmessageMessage::_IMMESSAGE_STATUS_SEND] = _CO_IMMESSAGE_MESSAGE_STATUS_SEND;
		if (!in_array($op, $options, false)) {
			$ret[\ImmessageMessage::_IMMESSAGE_STATUS_READ] = _CO_IMMESSAGE_MESSAGE_STATUS_READ;
			$ret[\ImmessageMessage::_IMMESSAGE_STATUS_TRASH] = _CO_IMMESSAGE_MESSAGE_STATUS_TRASH;
		}
		return $ret;
	}

	public function beforeSave(&$obj)
	{
		if ($obj->message_status !== \ImmessageMessage::_IMMESSAGE_STATUS_READ)
			$obj->setVar('message_modification_date', time());
		return true;
	}

}
