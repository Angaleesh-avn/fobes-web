<?php

namespace App\Http\Traits;

use App\Models\ApplicationGroup;
use App\Models\AppliedJob;
use App\Models\Job;
use Illuminate\Http\Request;


use App\Models\Candidate;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

trait HasCompanyApplication
{
    /**
     * Company job application sync
     *
     * @return Response
     */
    public function applicationsSync(Request $request)
    {
        $this->validate(request(), [
            'applicationGroups' => ['required', 'array'],
        ]);

        foreach ($request->applicationGroups as $applicationGroup) {
            foreach ($applicationGroup['applications'] as $i => $application) {
                $order = $i + 1;

                if ($application['application_group_id'] !== $applicationGroup['id'] || $application['order'] != $order) {
                    $applications = AppliedJob::where('id', $application['id'])
                        ->where('application_group_id', $application['application_group_id'])
                        ->first();

                    if ($applications) {
                        $applications->update([
                            'order' => $order,
                            'application_group_id' => $applicationGroup['id'],
                        ]);



                                    //mail for notification will be here whenever the drag changes done
                        
                                    // $getmsg = ApplicationGroup::where('id',$applicationGroup['id'])->first();
                                    // //print_r($getmsg->mail_text);exit();
                                    
                                    // //print_r($applications->candidate_id);exit();
                                    // $getcandidate = Candidate::where('id',$applications->candidate_id)->first();
                                    // $getuser = User::where('id',$getcandidate->user_id)->first();
                                    // $getemail = $getuser->email;
                                    // // print_r($getuser->email);exit();
                                    // $to = "shivaram@mindmade.in";
                                    // $subject = "Test Email";
                                    // // $message = "Hi" . $getuser->name;
                                    // // $message = "Hi " . $getuser->name . "<br>";
                                    // $message = "Hi " . $getuser->name . "\n";
                                    // $message .= $getmsg->mail_text;
                                    // $headers = "From: admin@fobes.in";
                                    // // $headers .= "Content-Type: text/html; charset=UTF-8\r\n";  //for html format
                                    // // Send email
                                    // $mailSent = mail($to, $subject, $message, $headers);



                                    // through smtp
                                    $getmsg = ApplicationGroup::where('id',$applicationGroup['id'])->first();
                                    $getcandidate = Candidate::where('id',$applications->candidate_id)->first();
                                    $getuser = User::where('id',$getcandidate->user_id)->first();
                                    $getname = $getuser->name;
                                    $getemail = $getuser->email;
                                    try {
                                        $recipientEmail = 'shivaram@mindmade.in';
                                        $subject = 'Your Job Application Updated';
                                        $message = "Hi " . $getname . "\n";
                                        $message .= $getmsg->mail_text;

                                        Mail::raw($message, function ($mail) use ($recipientEmail, $subject) {
                                            $mail->to($recipientEmail)
                                                 ->subject($subject);
                                        });
                                    } catch (\Exception $e) {
                                      dd($e->getMessage());
                                    }




                    }
                }
            }
        }

        return $request->user()
            ->company
            ->applicationGroups()
            ->with(['applications' => function ($query) {
                $query->with(['candidate' => function ($query) {
                    return $query->select('id', 'user_id', 'profession_id', 'experience_id', 'education_id')
                        ->with('profession', 'education:id', 'experience:id', 'user:id,name,username,image');
                }]);
            }])
            ->get();
    }

    /**
     * Company job application page
     *
     * @return Response
     */
    public function jobApplications(Request $request)
    {
        $application_groups = auth()->user()
            ->company
            ->applicationGroups()
            ->with(['applications' => function ($query) use ($request) {
                $query->where('job_id', $request->job)->with(['candidate' => function ($query) {
                    return $query->select('id', 'user_id', 'profession_id', 'experience_id', 'education_id')
                        ->with('profession', 'education:id', 'experience:id', 'user:id,name,username,image');
                }]);
            }])
            ->get();

        $job = Job::findOrFail($request->job, ['id', 'title', 'company_id']);
        abort_if(currentCompany()->id != $job->company_id, 404);

        return view('frontend.pages.company.draggable-application', compact('application_groups', 'job'));
    }

    /**
     * Application Column Store
     *
     * @return \Illuminate\Http\Response
     */
    public function applicationColumnStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        ApplicationGroup::create([
            'company_id' => auth()->user()->company->id,
            'name' => $request->name,
            // 'mail_text' => $request->mail_text,
        ]);

        flashSuccess(__('group_created_successfully'));

        return response()->json(['success' => true]);
    }

    /**
     * Application Column Update
     *
     * @return \Illuminate\Http\Response
     */
    public function applicationColumnUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        ApplicationGroup::find($request->id)->update([
            'name' => $request->name,
        ]);

        flashSuccess(__('group_updated_successfully'));

        return response()->json(['success' => true]);
    }

    /**
     * Application Column Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function applicationColumnDelete(ApplicationGroup $group)
    {
        if ($group->is_deleteable) {
            $new_group = ApplicationGroup::where('company_id', auth()->user()->company->id)
                ->where('id', '!=', $group->id)
                ->where('is_deleteable', false)
                ->first();

            if ($new_group) {
                $group->applications()->update([
                    'application_group_id' => $new_group->id,
                ]);
            }

            $group->delete();

            response()->json(['success' => true, 'message' => __('group_deleted_successfully')]);
        }

        response()->json(['success' => false, 'message' => __('group_is_not_deletable')]);
    }

    /**
     * Company Delete Applications
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyApplication(Job $job, Request $request)
    {
        $job->appliedJobs()->detach($request->candidate_id);

        flashSuccess(__('application_removed_from_our_system'));

        return back();
    }
}
