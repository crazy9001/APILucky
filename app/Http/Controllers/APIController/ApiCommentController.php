<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/17/2018
 * Time: 12:36 PM
 */

namespace App\Http\Controllers\APIController;


use App\Services\Repositories\Interfaces\CommentInterface;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;
use Log;

class ApiCommentController extends BaseApiController
{
    protected $commentRepository;

    /**
     * ApiCommentController constructor.
     * @param CommentInterface $commentRepository
     */
    public function __construct(CommentInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = Input::get('status');
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $offset = ($pageId - 1) * $limit;
        $filters = array(
            'limit' => trim($limit),
            'offset' => trim($offset),
            'status'  => $status
        );
        $query = $this->commentRepository->getAllComment($filters);
        $comments = $query->get();
        return $this->sendResponse($comments, 'Success');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required',
            'contentMessage' =>  'required',
        ],
        [
            'fullName.required' =>  'Chưa nhập họ tên',
            'contentMessage.required'   =>  'Chưa nhập nội dung'
        ]);
        if( $validator->fails() ){
            return $this->sendError($validator->errors()->first(), 400);
        }

        $comment = $this->commentRepository->getModel();
        $comment->fill($request->all());
        $comment->avatar = isset($request->avatar) && !empty($request->avatar) ? $request->avatar : '/storage/avatar.gif';
        $comment->typeGift = 0;
        $comment->status = 0;
        //save comment
        $comment = $this->commentRepository->createOrUpdate($comment);
        return $this->sendResponse($comment, 'Success');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function approveComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ],
        [
            'id.required' =>  'Chưa chọn comment',
        ]);
        if( $validator->fails() ){
            return $this->sendError($validator->errors()->first(), 400);
        }

        $comment = $this->commentRepository->findById($request->id);
        $comment->status = 1;
        $this->commentRepository->createOrUpdate($comment);
        return $this->sendResponse($comment, 'Success');

    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = $this->commentRepository->getFirstBy(['id' => $id]);
        if($comment){
            return $this->sendResponse($comment, 'Success');
        }
        return $this->sendError('Comment không tồn tại', 400);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = $this->commentRepository->getFirstBy(['id' => $id]);
        if($comment){
            $comment->delete();
            return $this->sendResponse('Đã xóa comment', 200);
        }
        return $this->sendError('Comment không tồn tại', 400);
    }

}