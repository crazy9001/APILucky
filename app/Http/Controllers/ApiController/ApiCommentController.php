<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/17/2018
 * Time: 12:36 PM
 */

namespace App\Http\Controllers\ApiController;


use App\Services\Repositories\Interfaces\CommentInterface;
use Carbon\Carbon;
use Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;
use Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
        $images = Input::get('images');
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $offset = ($pageId - 1) * $limit;
        $filters = array(
            'limit' => trim($limit),
            'offset' => trim($offset),
            'status'  => $status,
            'images'    => $images
        );
        $query = $this->commentRepository->getAllComment($filters);
        $comments = $query->get();
        return $this->sendResponse($comments, 'Success');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar'    =>'required',
            'fullName' => 'required',
            'contentMessage' =>  'required',
            'file' => 'sometimes|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ],
        [
            'avatar.required'   =>  'Chưa nhập avatar',
            'fullName.required' =>  'Chưa nhập họ tên',
            'contentMessage.required'   =>  'Chưa nhập nội dung',
            'file.required' =>  'Vui lòng chọn file',
            'file.mimes'    =>  'Dữ liệu không hợp lệ',
            'file.max'  =>  'Dữ liệu quá lớn'
        ]);
        $path = null;
        if($request->file('file')){
            $path = $request->file('file')->store('file');
        }
        if( $validator->fails() ){
            return $this->sendError($validator->errors()->first(), 400);
        }
        $findLastComment = $this->commentRepository->getLastCommentbyUserId($request->avatar);
        if($findLastComment){
            $timeLastComment = $findLastComment->created_at;
            $diff_in_minutes = $timeLastComment->diffInMinutes(Carbon::now());
        }else{
            $diff_in_minutes = 1;
        }
        if($diff_in_minutes >= 1){
            $comment = $this->commentRepository->getModel();
            $comment->fill($request->all());
            $comment->userId = $request->avatar;
            $comment->giftImage = isset($request->giftImage) && !empty($request->giftImage) ? $request->giftImage : url('/') . config('base.default_gift');
            $comment->typeGift = 0;
            $comment->status = 0;
            $comment->images = $path;
            //save comment
            $comment = $this->commentRepository->createOrUpdate($comment);
            return $this->sendResponse($comment, 'Success');
        }
        return $this->sendError('Vui lòng chờ 1 phút để tiếp tục', 400);
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

        $comment = $this->commentRepository->getFirstBy(['id' => $request->id]);
        if($comment){
            $comment->status = 1;
            $this->commentRepository->createOrUpdate($comment);

            $client = new Client();
            try{
                $client->get('https://recorder.vtv.vn/api/users', [
                    'query' =>  [
                        'avatar'    =>  $comment->avatar,
                        'fullName'  =>  $comment->fullName,
                        'contentMessage'    =>  $comment->contentMessage,
                        'images'    =>  $comment->images
                    ]
                ]);
            }catch (RequestException $e){
                $response = $e->getResponse();
                return $this->sendError($response, 400);
            }

            return $this->sendResponse($comment, 'Success');
        }
        return $this->sendError('Comment không tồn tại', 400);
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

    public function getTopComment()
    {
        $status = Input::get('status');
        $images = Input::get('images');
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $offset = ($pageId - 1) * $limit;
        $filters = array(
            'limit' => trim($limit),
            'offset' => trim($offset),
            'status'  => $status,
            'images'    => $images
        );
        $query = $this->commentRepository->getTopComment($filters);
        $comments = $query->get();
        return $this->sendResponse($comments, 'Success');
    }

}