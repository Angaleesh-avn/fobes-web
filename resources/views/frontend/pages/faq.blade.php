@extends('frontend.layouts.app')

@section('description')
    @php
        $data = metaData('faq');
    @endphp
    {{ $data->description }}
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
    {{ $data->title }}
@endsection

@section('main')
<div class="breadcrumbs breadcrumbs-height">
    <div class="container">
      <div class="row align-items-center breadcrumbs-height">
        <div class="col-12 justify-content-center text-center">
          <div class="breadcrumb-title rt-mb-10">   {{ __('faq') }}</div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a href="{{ route('website.home') }}">  {{ __('home') }}</a></li>
              <li class="breadcrumb-item active" aria-current="page">   {{ __('faq') }}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!--Faq Starts-->
  <div class="container" style="margin-bottom: 48px;">
    <div class="tw-max-w-[648px] mx-auto faq-page tw-py-8">
      @foreach ($faq_categories as $cat)
            <div class="rt-faq rt-pt-30 rt-pt-md-30">
                @if(count($cat->faqs) > 0)
                    <h6 class="ft-wt-5 tw-text-2xl text-primary-500 text-capitalize rt-mb-24">{{$cat->name}}</h6>
                @endif
                @foreach ($cat->faqs as $faq)
                    <div class="accordion rt-mb-24 ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                <button class="accordion-button accordion-pad body-font-2 text-gray-900 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="true" aria-controls="collapse{{ $faq->id }}">
                                    {{$faq->question}}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $faq->id }}">
                                <div class="accordion-body accordion-pad">
                                    {!! $faq->answer !!}
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
<!--faq to add here-->
<h4>Candidates FAQs</h4>
<h6>1. How is Fobes different from other traditional portals?</h6>
Traditional hiring is long, expensive, and inefficient. Fobes simplifies the process
by connecting employers directly with job seekers with relevant skills and
experience.
<br>
<h6>2. How can I get the best company from your portal?</h6>
Fobes has over 5000 active companies across 70+ job categories. Our AI algorithm
selects the best-fit company for your resume.
<br>
<h6>3. Do I need to pay to apply to a job or get an Interview call?</h6>
No. You can apply to jobs for FREE on Fobes
<br>
<h6>4. Recruiters are asking me to pay to schedule interview for job?</h6>
Note that genuine recruiters do not ask for money to schedule interviews or offer a
job. If you are receiving such calls or emails, beware as this might be a job scam.
<br>
<h4>Recruiters FAQs</h4>
<h6>5. Why is my job being under a review?</h6>
We promise to endeavor that your job is made active at the earliest. Few job
approval decisions can take up to 1 day due to a delay in verifying your account.
Until your account KYC gets completed, your jobs will remain under review.
<br>
<h6>6. Why the documents are required?</h6>
Most job portals do not verify if someone is using your name or your company’s
name to list jobs and defraud job seekers. Often times it affects the company’s
reputation as an employer in the market. But on Fobes we verify the identity of the
recruiter and their association with the company before their job post is active on
the platform. 
<br>
<h6>7. How do I post a job?</h6>
<ul>
    

<li>To post a job you must be logged in to the employer dashboard with your
mobile number </li>
<li>Under &quot;Jobs&quot; Menu, click on Post a Job and fill in the job criteria.</li>
<li>On the Job Details page, select your Job role, Department, Category of the
job, and type of job. </li>
<li>You can also select the job location, compensation and salary range from
this page</li>
<li>On the Candidate Requirements page, select the minimum education level,
total experience, the job titles of the candidates and the English level that are
required for the job. </li>
<li>In Additional Requirements, you can add age, gender, skills, regional
language, degree, assets and industry preferences required for the job, if any,
and your job description.</li>
<li>On the Interviewer information page, select the interviewer details, interview
method, and interview address, and select communication preferences on
how you want to contact the candidates.</li>
<li>Preview your job thoroughly as these are the details applicants will see
before applying.</li>
<li>Select a plan and Agree to our employer code of conduct and click on Post
Job with xxx plans.</li>
<li>If you do not have sufficient balance in your employer account, you may be
prompted get subscription plans.</li>
</ul>
<br>
<h6>8. How long will it take for my job to go live?</h6>
We assure you that we will make every effort to activate your job as soon as
possible. Please note that certain job approval decisions may take up to 1 day due
to a delay in verifying your account. Your jobs will remain under review until your
account&#39;s KYC verification is completed.
If your KYC verification is not pending. You can also contact our customer support
on WhatsApp ( xxxxxxxxxxxxx ) from 9 am to 6 pm every day.

<h6>9. What is the meaning of unlimited applications?</h6>
Unlimited job applications refer to a feature that allows you to receive an
unrestricted number of applications from potential candidates for your job posting.
There are no limitations or restrictions placed on the number of applications you
can receive.
10. How can I boost my job? What is Smart Boost Via WhatsApp?
To boost your job, you can upgrade to the Premium plan and post your job using
that plan. Boosting is not available for jobs posted with the Classic plan. By
boosting your job, you can enhance its visibility and attract a larger pool of
qualified candidates.
<h6>11. What is job branding?</h6>
Job branding refers to the process of creating a unique and compelling image for a
particular job. It involves positioning the job in a way that sets it apart from similar
roles and attracts qualified candidates.
<h6>12. How can I contact Fobes customer care?</h6>
Call or WhatsApp: 9385245210,9385245296
    
  </div>
  <!-- Faq End-->

{{-- Subscribe Newsletter  --}}
<x-website.subscribe-newsletter/>
@endsection
