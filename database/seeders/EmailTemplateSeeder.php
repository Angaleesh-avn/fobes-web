<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email_templates = [
            [
                'name' => 'New User',
                'type' => 'new_user',
                'subject' => 'Welcome {user_name}',
                'message' => "<p>Hi {user_name},</p><p>Welcome to {company_name}. It's great to have you here!</p><p>Have a great time!</p><p>Regards,<br>{company_name} team</p>",
            ],
            [
                'name' => 'Edited Job',
                'type' => 'new_edited_job_available',
                'subject' => 'New Edited Job Available For Approval!',
                'message' => '<p>Hello <strong>{admin_name}</strong>,<br>A new edited job available for approval!</p>',
            ],
            [
                'name' => 'New Job Available',
                'type' => 'new_job_available',
                'subject' => 'New Job Available For Approval!',
                'message' => '<p>Hello {admin_name},<br>A new job available for approval!</p>',
            ],
            [
                'name' => 'New Plan Purchase',
                'type' => 'new_plan_purchase',
                'subject' => '{user_name} Has Purchased The {plan_label} Plan!',
                'message' => '<p>{user_name} Has Purchased The {plan_label} Plan!</p>',
            ],
            [
                'name' => 'New User Registered',
                'type' => 'new_user_registered',
                'subject' => 'New {user_role} Registered!',
                'message' => '<p>Hello {admin_name},<br>A {user_role} Registered Recently!</p>',
            ],
            [
                'name' => 'Plan Purchase',
                'type' => 'plan_purchase',
                'subject' => 'Plan Purchased',
                'message' => '<p>Hello {user_name}!<br>You purchase of {plan_type} has been successfully completed!<br>Regards</p>',
            ],
            [
                'name' => 'New Pending Candidate',
                'type' => 'new_pending_candidate',
                'subject' => 'Candidate Created',
                'message' => '<p>Hello {user_name},<br><br>Your candidate profile has been created and is waiting for admin approval.<br><br>Please login with your credentials below to check status -<br>Your Email : {user_email}<br>Your Password : {user_password}<br><br>Regards</p>',
            ],
            [
                'name' => 'New Candidate',
                'type' => 'new_candidate',
                'subject' => 'Candidate Created',
                'message' => '<p>Hello {user_name},<br><br>Your candidate profile has been created.<br><br>Please login with your credentials below to check status -<br>Your Email : {user_email}<br>Your Password : {user_password}<br><br>Regards</p>',
            ],
            [
                'name' => 'New Company Pending',
                'type' => 'new_company_pending',
                'subject' => 'Company created and waiting for admin approval',
                'message' => '<p>Hello {user_name},<br><br>Your company profile has been created and is waiting for admin approval.<br><br>Please check back your account with the login information below -<br>Your Email : {user_email}<br>Your Password : {user_password}<br><br>Regards</p>',
            ],
            [
                'name' => 'New Company',
                'type' => 'new_company',
                'subject' => 'Company Created',
                'message' => '<p>Hello {user_name},<br><br>Your company profile has been created. Please login with below information.<br><br>Please check back your account with the login information below -<br>Your Email : {user_email}<br>Your Password : {user_password}<br><br>Regards</p>',
            ],
            [
                'name' => 'Update Company Password',
                'type' => 'update_company_pass',
                'subject' => '{account_type} Updated',
                'message' => '<p>Hello {user_name},<br><br>Your {account_type} profile password updated.<br><br>Your Email : {user_email}<br>Your password : {password}<br><br>Regards</p>',
            ],
            [
                'name' => 'Verify Subscription Notification',
                'type' => 'verify_subscription_notification',
                'subject' => 'Verify Your Subscription',
                'message' => "<p>Thanks for your interest in our newsletter!</p><p>You're one step away</p><h2>verify your email address</h2><p>to subscribe our newletter.</p><h3><a href='{verify_subscription}'>Verify Now</a>&nbsp;</h3><p>Regards</p>",
            ],
        ];

        foreach ($email_templates as $email_template) {
            EmailTemplate::create($email_template);
        }

    }
}
