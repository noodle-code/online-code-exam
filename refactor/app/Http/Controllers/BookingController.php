<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if(
            $request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') ||
            $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID')
        )
        {
            return response($this->bookingRepository->getAll($request));
        }

        return response($this->bookingRepository->getUsersJobs($request->get('user_id')));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return response(
            $this->bookingRepository->with('translatorJobRel.user')
                                    ->find($id)
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        return response(
            $this->bookingRepository->store($request->__authenticatedUser, $request->all())
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $data = array_except($request->all(), ['_token', 'submit']);

        return response(
            $this->bookingRepository->updateJob($id, $data, $request->__authenticatedUser)
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        return response(
            $this->bookingRepository->storeJobEmail($request->all())
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        return response(
            $this->bookingRepository->getUsersJobsHistory(
                $request->get('user_id'), 
                $request
            )
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        return response(
            $this->bookingRepository->acceptJob(
                $request->all(), 
                $request->__authenticatedUser
            )
        );
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJobWithId(Request $request)
    {
        return response(
            $this->bookingRepository->acceptJobWithId(
                $request->get('job_id'), 
                $request->__authenticatedUser
            )
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        return response(
            $this->bookingRepository->cancelJobAjax(
                $request->all(),
                $request->__authenticatedUser
            )
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        return response($this->bookingRepository->endJob($request->all()));

    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function customerNotCall(Request $request)
    {
        return response($this->bookingRepository->customerNotCall($request->all()));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        return response($this->bookingRepository->getPotentialJobs($request->__authenticatedUser));
    }

    // Note: logic inside this handler method should be transferred to the repository class.
    /**
     * 
     * @param Request $request
     * @return mixed
     */
    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        if (isset($data['distance']) && $data['distance'] != "") {
            $distance = $data['distance'];
        } else {
            $distance = "";
        }
        if (isset($data['time']) && $data['time'] != "") {
            $time = $data['time'];
        } else {
            $time = "";
        }
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobid = $data['jobid'];
        }

        if (isset($data['session_time']) && $data['session_time'] != "") {
            $session = $data['session_time'];
        } else {
            $session = "";
        }

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }
        
        if ($data['manually_handled'] == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        if (isset($data['admincomment']) && $data['admincomment'] != "") {
            $admincomment = $data['admincomment'];
        } else {
            $admincomment = "";
        }
        if ($time || $distance) {

            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));

        }

        return response('Record updated!');
    }

    /**
     * 
     * @param Request $request
     * @return mixed
     */
    public function reopen(Request $request)
    {
        return response($this->bookingRepository->reopen($request->all()));
    }

    /**
     * 
     * @param Request $request
     * @return mixed
     */
    public function resendNotifications(Request $request)
    {
        // $data = $request->all();
        // $job = $this->bookingRepository->find($data['jobid']);
        // $job_data = $this->bookingRepository->jobToData($job);

        // $job_data = $this->bookingRepository->jobToData($job) 

        // $this->bookingRepository->sendNotificationTranslator($job, $job_data, '*');

        try {
            $this->bookingRepository->sendNotifToTranslator($request->get('jobid'));
            return response(['success' => 'Notification succesfully sent.']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        /*$data = $request->all();
        $job = $this->bookingRepository->find($data['jobid']);
        $job_data = $this->bookingRepository->jobToData($job);*/

        try {
            $this->bookingRepository->sendSMSNotificationToTranslator($request->get('jobid'));
            return response(['success' => 'SMS successfully sent.']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
