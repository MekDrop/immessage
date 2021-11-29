<?php

namespace ImpressCMS\Modules\ImpressModules\immessage\Controllers;

use icms;
use ImmessageMessage;
use ImmessageMessageHandler;
use Imponeer\Database\Criteria\CriteriaCompo;
use Imponeer\Database\Criteria\CriteriaElement;
use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\Condition;
use ImpressCMS\Core\Message;
use ImpressCMS\Core\Response\RedirectResponse;
use ImpressCMS\Core\Response\ViewResponse;
use ImpressCMS\Core\View\Table\Column;
use ImpressCMS\Core\View\Table\Table;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Router\Annotation\Route;
use Sunrise\Http\Router\Router;
use Symfony\Contracts\Translation\TranslatorInterface;
use function icms_getModuleHandler;

class DefaultController
{

	/**
	 * Shows list page
	 *
	 * @param ServerRequestInterface $request
	 * @param CriteriaElement $statusCriteria
	 *
	 * @return ResponseInterface
	 *
	 * @throws \SmartyException
	 */
	protected function showListPage(ServerRequestInterface $request, CriteriaElement $statusCriteria) {
		if (!icms::$user) {
			/**
			 * @var ResponseFactoryInterface $responseFactory
			 */
			$responseFactory = icms::getInstance()->get('response_factory');
			return $responseFactory->createResponse(404);
		}

		/**
		 * @var TranslatorInterface $translator
		 */
		$translator = icms::getInstance()->get('translator');

		/**
		 * @var ImmessageMessageHandler $msgHandler
		 */
		$msgHandler = icms_getModuleHandler('message');

		$criteria = new CriteriaCompo();
		$criteria->add(
			$statusCriteria,
			Condition::AND()
		);
		$criteria->add(
			new CriteriaItem('message_to_uid', icms::$user->uid),
			Condition::AND()
		);
		$criteria->add(
			new CriteriaItem('message_show_on_inbox', 1),
			Condition::AND()
		);

		$table = new Table($msgHandler, $criteria, [], true);
		$table->setDefaultOrder('message_modification_date');
		$table->addColumn(
			new Column('message_status', 'center')
		);
		$table->addColumn(
			new Column('message_from_uid', 'left')
		);
		$table->addColumn(
			new Column('message_title', 'center')
		);
		$table->addColumn(
			new Column('message_modification_date', 'center')
		);

		$table->addIntroButton(
			'addmenu',
			'message.php?op=mod',
			$translator->trans('_CO_IMMESSAGE_MESSAGE_COMPOSE', [], 'common')
		);

		$table->addQuickSearch(array('message_title',"message_content"));

		$table->addCustomAction('getMessageResponseButton');
		$table->addCustomAction('getMessageForwardButton');
		$table->addCustomAction('getMessageTrashButton');

		$response = new ViewResponse();
		$response->assign(
			'icms_contents',
			$table->render()
		);

		return $response;
	}

	/**
	 * View private messages list
	 *
	 * @Route(
	 *     name="private_message_inbox",
	 *     path="/viewpmsg.php",
	 *     methods={"GET", "POST"},
	 *     priority=1000
	 * )
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \SmartyException
	 */
	public function showViewPrivateMessagePage(ServerRequestInterface $request): ResponseInterface {
		$statusCriteria = new CriteriaCompo();
		$statusCriteria->add(
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_SEND)
		);
		$statusCriteria->add(
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_READ),
			Condition::OR()
		);

		return $this->showListPage($request, $statusCriteria);
	}

	/**
	 * Show trash
	 *
	 * @Route(
	 *     name="private_message_trash",
	 *     path="/trash.php",
	 *     methods={"GET", "POST"},
	 *     priority=1000
	 * )
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \SmartyException
	 */
	public function showTrash(ServerRequestInterface $request): ResponseInterface {
		return $this->showListPage(
			$request,
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_TRASH)
		);
	}

	/**
	 * Show drafts page
	 *
	 * @Route(
	 *     name="private_message_drafts",
	 *     path="/drafts.php",
	 *     methods={"GET", "POST"},
	 *     priority=1000
	 * )
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \SmartyException
	 */
	public function showDrafts(ServerRequestInterface $request): ResponseInterface {
		return $this->showListPage(
			$request,
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_DRAFT)
		);
	}

	/**
	 * Show sent page
	 *
	 * @Route(
	 *     name="private_message_sent",
	 *     path="/sent.php",
	 *     methods={"GET", "POST"},
	 *     priority=1000
	 * )
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \SmartyException
	 */
	public function showSent(ServerRequestInterface $request): ResponseInterface {
		$criteriaStatus = new CriteriaCompo();
		$criteriaStatus->add(
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_SEND)
		);
		$criteriaStatus->add(
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_READ),
			Condition::OR()
		);
		$criteriaStatus->add(
			new CriteriaItem('message_status', ImmessageMessage::_IMMESSAGE_STATUS_TRASH),
			Condition::OR()
		);

		return $this->showListPage($request, $criteriaStatus);
	}

	/**
	 * Shows response form
	 *
	 * @param ServerRequestInterface $request
	 * @param string $prefixTitle
	 * @param string $sendTitle
	 * @return ResponseInterface
	 */
	protected function showResponseForm(ServerRequestInterface $request, string $prefixTitle, string $sendTitle): ResponseInterface {
		/** @noinspection AdditionOperationOnArraysInspection */
		$params = $request->getParsedBody() + $request->getQueryParams();

		$response = new ViewResponse([
			'template_main' => 'immessage_message.html',
		]);

		/**
		 * @var ImmessageMessageHandler $msgHandler
		 */
		$msgHandler = icms_getModuleHandler('message');

		/**
		 * @var ImmessageMessage $message
		 */
		$message = $msgHandler->get($params['message_id']);
		$message->message_id = 0;
		$message->message_title = $prefixTitle . $message->message_title;
		$message->message_content = '<blockquote>' . $message->message_content . '</blockquote>';
		$message->message_from_uid = \icms::$user->uid;

		/**
		 * @var TranslatorInterface $translator
		 */
		$translator = icms::getInstance()->get('translator');

		$sform = $message->getForm(
			$translator->trans($sendTitle, [], 'common'),
			'addmessage'
		);
		$sform->assign($response);

		return $response;
	}

	/**
	 * Shows page for writing a response
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function writeResponse(ServerRequestInterface $request): ResponseInterface {
		return $this->showResponseForm($request, "RE: ", '_CO_IMMESSAGE_SEND_RESPONSE');
	}

	/**
	 * Forward existing message
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function forward(ServerRequestInterface $request): ResponseInterface {
		return $this->showResponseForm($request, "FWD: ", '_CO_IMMESSAGE_SEND_FORWARD');
	}

	/**
	 * PMLite
	 *
	 * @Route(
	 *     name="private_message_create",
	 *     path="/pmlite.php",
	 *     methods={"GET", "POST"},
	 *     priority=1000
	 * )
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 */
	public function showPMLitePage(ServerRequestInterface $request): ResponseInterface {
		/** @noinspection AdditionOperationOnArraysInspection */
		$params = $request->getParsedBody() + $request->getQueryParams();

		switch ($params['op']) {
			case 'forward':
				return $this->forward($request);
			case 'response':
				return $this->writeResponse($request);
			case 'dodfsent':
				return $this->deleteMessageFromSent($request);
			case 'dodelete':
				return $this->doDelete($request);
			case 'dotrash':
				return $this->doTrashAction($request);
			case 'mod':
				return $this->edit($request);
			case 'trash':
				return $this->confirmTrashAction($request);
		}

		$response = new ViewResponse([
			'template_main' => 'immessage_message.html',
		]);

		/**
		 * @var ImmessageMessageHandler $msgHandler
		 */
		$msgHandler = icms_getModuleHandler('message');

		/**
		 * @var ImmessageMessage $messageObj
		 */
		$messageObj = $msgHandler->get($params['message_id']);

		if($messageObj->message_status !== ImmessageMessage::_IMMESSAGE_STATUS_READ){
			$messageObj->setVar('message_status',ImmessageMessage::_IMMESSAGE_STATUS_READ);
			$msgHandler->insert($messageObj, true);
		}
		$response->assign(
			"message_title",
			$messageObj->getVar('message_title')
		);
		$response->assign(
			"message_actions",
			$messageObj->getButtons()
		);
		$response->assign(
			"message_content",
			$messageObj->getVar('message_content')
		);
		$response->assign(
			"message_from",
			$messageObj->getVar('message_from_uid')
		);
		$response->assign(
			"message_subject",
			$messageObj->getVar('message_title')
		);
		$response->assign(
			"message_to",
			$messageObj->getVar('message_to_uid')
		);
		$response->assign(
			"message_modification_date",
			$messageObj->getVar('message_modification_date')
		);

		return $response;
	}

	/**
	 * Does action with message and redirects
	 *
	 * @param ServerRequestInterface $request
	 * @param array $fieldsToUpdate
	 * @param string $redirectRouteName
	 * @param string $redirectMessage
	 *
	 * @return RedirectResponse
	 */
	protected function doActionWithMessage(
		ServerRequestInterface $request,
		array $fieldsToUpdate,
		string $redirectRouteName,
		string $redirectMessage
	): RedirectResponse
	{
		/** @noinspection AdditionOperationOnArraysInspection */
		$params = $request->getParsedBody() + $request->getQueryParams();

		/**
		 * @var ImmessageMessageHandler $msgHandler
		 */
		$msgHandler = icms_getModuleHandler('message');

		/**
		 * @var ImmessageMessage $messageObj
		 */
		$messageObj = $msgHandler->get($params['message_id']);

		foreach ($fieldsToUpdate as $k => $v) {
			$messageObj->$k = $v;
		}
		$messageObj->store();

		/**
		 * @var TranslatorInterface $translator
		 */
		$translator = icms::getInstance()->get('translator');

		/**
		 * @var Router $router
		 */
		$router = icms::getInstance()->get('router');

		return new RedirectResponse(
			$router->generateUri($redirectRouteName),
			301,
			$translator->trans($redirectMessage, [], 'common')
		);
	}

	/**
	 * Deletes message from sent folder
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return RedirectResponse
	 */
	public function deleteMessageFromSent(ServerRequestInterface $request)
	{
		return $this->doActionWithMessage(
			$request,
			[
				'message_show_on_sent' => 0
			],
			'private_message_sent',
			'_CO_IMMESSAGE_MESSAGE_DELETED_FROM_SENT'
		);
	}

	/**
	 * Actually deletes message
	 *
	 * @param ServerRequestInterface $request
	 */
	public function doDelete(ServerRequestInterface $request): RedirectResponse
	{
		return $this->doActionWithMessage(
			$request,
			[
				'message_show_on_inbox' => 0,
				'message_status' =>  ImmessageMessage::_IMMESSAGE_STATUS_DELETED,
			],
			'private_message_inbox',
			'_CO_IMMESSAGE_MESSAGE_DELETED_FROM_TRASH'
		);
	}

	/**
	 * Sends a message to trash
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return RedirectResponse
	 */
	public function doTrashAction(ServerRequestInterface $request)
	{
		return $this->doActionWithMessage(
			$request,
			[
				'message_show_on_inbox' => 0,
				'message_status' =>  ImmessageMessage::_IMMESSAGE_STATUS_TRASH,
			],
			'private_message_inbox',
			'_CO_IMMESSAGE_MESSAGE_SENT_TO_TRASH'
		);
	}

	/**
	 * Create/edit form
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ViewResponse
	 */
	public function edit(ServerRequestInterface $request)
	{
		/** @noinspection AdditionOperationOnArraysInspection */
		$params = $request->getParsedBody() + $request->getQueryParams();

		$response = new ViewResponse([
			'template_main' => 'immessage_message.html',
		]);

		/**
		 * @var ImmessageMessageHandler $msgHandler
		 */
		$msgHandler = icms_getModuleHandler('message');

		/**
		 * @var ImmessageMessage $msg
		 */
		$msg = $msgHandler->get($params['message_id']);

		/**
		 * @var TranslatorInterface $translator
		 */
		$translator = icms::getInstance()->get('translator');

		$form = $msg->getForm(
			$translator->trans(
				$msg->isNew() ? '_CO_IMMESSAGE_MESSAGE_CREATE' : '_CO_IMMESSAGE_MESSAGE_EDIT',
				[],
				'common'
			),
			'addmessage'
		);
		$form->assign($response);

		return $response;
	}

	protected function doConfirmAction(array $data) {
		Message::confirm($data, );
		xoops_confirm(array('op' => 'dotrash','message_id'=> $params['message_id']), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_SEND_TRASH_CONFIRM);
	}

	public function confirmTrashAction(ServerRequestInterface $request)
	{
		$params = $request->getParsedBody();

		xoops_confirm(array('op' => 'dotrash','message_id'=> $params['message_id']), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_SEND_TRASH_CONFIRM);
	}
}