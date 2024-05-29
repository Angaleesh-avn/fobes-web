<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use Illuminate\Http\Request;
use Modules\SetupGuide\Entities\SetupGuide;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only(['update', 'manualPaymentUpdate', 'manualPaymentDelete', 'manualPaymentStatus']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autoPayment()
    {
        abort_if(! userCan('setting.view'), 403);

        return view('backend.settings.pages.payment');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            switch ($request->type) {
                case 'paypal':
                    $this->paypalUpdate($request);
                    break;
                case 'stripe':
                    $this->stripeUpdate($request);
                    break;
                case 'razorpay':
                    $this->razorpayUpdate($request);
                    break;
                case 'ssl_commerz':
                    $this->sslcommerzUpdate($request);
                    break;
                case 'paystack':
                    $this->paystackUpdate($request);
                    break;
                case 'flutterwave':
                    $this->flutterwaveUpdate($request);
                    break;
                case 'midtrans':
                    $this->midtransUpdate($request);
                    break;
                case 'mollie':
                    $this->mollieUpdate($request);
                    break;
                case 'instamojo':
                    $this->instamojoUpdate($request);
                    break;
            }

            SetupGuide::where('task_name', 'payment_setting')->update(['status' => 1]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the paypal configuration.
     */
    public function paypalUpdate(Request $request)
    {
        $request->validate([
            'paypal_client_id' => 'required',
            'paypal_client_secret' => 'required',
        ]);

        try {
            if ($request->paypal_live_mode) {
                checkSetEnv('PAYPAL_LIVE_CLIENT_ID', $request->paypal_client_id);
                checkSetEnv('PAYPAL_LIVE_CLIENT_SECRET', $request->paypal_client_secret);
            } else {
                checkSetEnv('PAYPAL_SANDBOX_CLIENT_ID', $request->paypal_client_id);
                checkSetEnv('PAYPAL_SANDBOX_CLIENT_SECRET', $request->paypal_client_secret);
            }

            setEnv('PAYPAL_MODE', $request->paypal_live_mode ? 'live' : 'sandbox');
            setEnv('PAYPAL_ACTIVE', $request->paypal ? 'true' : 'false');

            flashSuccess(__('paypal_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the stripe configuration.
     */
    public function stripeUpdate(Request $request)
    {
        $request->validate([
            'stripe_key' => 'required',
            'stripe_secret' => 'required',
        ]);

        try {
            checkSetEnv('STRIPE_KEY', $request->stripe_key);
            checkSetEnv('STRIPE_SECRET', $request->stripe_secret);
            setEnv('STRIPE_ACTIVE', $request->stripe ? 'true' : 'false');

            flashSuccess(__('stripe_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the razorpay configuration.
     */
    public function razorpayUpdate(Request $request)
    {
        try {
            $request->validate([
                'razorpay_key' => 'required',
                'razorpay_secret' => 'required',
            ]);

            checkSetEnv('RAZORPAY_KEY', $request->razorpay_key);
            checkSetEnv('RAZORPAY_SECRET', $request->razorpay_secret);
            setEnv('RAZORPAY_ACTIVE', $request->razorpay ? 'true' : 'false');

            flashSuccess(__('razorpay_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the sslcommerz configuration.
     */
    public function sslcommerzUpdate(Request $request)
    {
        $request->validate([
            'store_id' => 'required',
            'store_password' => 'required',
        ]);

        try {
            checkSetEnv('SSLCOMMERZ_MODE', $request->ssl_mode ? 'live' : 'sandbox');
            checkSetEnv('STORE_ID', $request->store_id);
            checkSetEnv('STORE_PASSWORD', $request->store_password);
            setEnv('SSLCOMMERZ_ACTIVE', $request->ssl_commerz ? 'true' : 'false');

            flashSuccess(__('ssl_commerz_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the paystack configuration.
     */
    public function paystackUpdate(Request $request)
    {
        $request->validate([
            'paystack_public_key' => 'required',
            'paystack_secret_key' => 'required',
            'merchant_email' => 'required',
        ]);

        try {
            checkSetEnv('PAYSTACK_PUBLIC_KEY', $request->paystack_public_key);
            checkSetEnv('PAYSTACK_SECRET_KEY', $request->paystack_secret_key);
            checkSetEnv('MERCHANT_EMAIL', $request->merchant_email);
            setEnv('PAYSTACK_ACTIVE', $request->paystack ? 'true' : 'false');

            flashSuccess(__('paystack_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the flutterwave configuration.
     */
    public function flutterwaveUpdate(Request $request)
    {
        $request->validate([
            'flw_public_key' => 'required',
            'flw_secret_key' => 'required',
            'flw_secret_hash' => 'required',
        ]);

        try {
            checkSetEnv('FLW_PUBLIC_KEY', $request->flw_public_key);
            checkSetEnv('FLW_SECRET_KEY', $request->flw_secret_key);
            checkSetEnv('FLW_SECRET_HASH', $request->flw_secret_hash);
            setEnv('FLW_ACTIVE', $request->flutterwave ? 'true' : 'false');

            flashSuccess(__('flutter_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the midtrans configuration.
     */
    public function midtransUpdate(Request $request)
    {
        $request->validate([
            'midtrans_merchat_id' => 'required',
            'midtrans_client_key' => 'required',
            'midtrans_server_key' => 'required',
        ]);

        try {
            checkSetEnv('MIDTRANS_MERCHAT_ID', $request->midtrans_merchat_id);
            checkSetEnv('MIDTRANS_CLIENT_KEY', $request->midtrans_client_key);
            checkSetEnv('MIDTRANS_SERVER_KEY', $request->midtrans_server_key);
            setEnv('MIDTRANS_ACTIVE', $request->midtrans ? 'true' : 'false');
            setEnv('MIDTRANS_LIVE_MODE', $request->midtrans_live_mode ? 'true' : 'false');

            flashSuccess(__('midtrans_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the mollie configuration.
     */
    public function mollieUpdate(Request $request)
    {
        $request->validate([
            'mollie_key' => 'required',
        ]);

        try {
            checkSetEnv('MOLLIE_KEY', $request->mollie_key);
            setEnv('MOLLIE_ACTIVE', $request->mollie ? 'true' : 'false');

            flashSuccess(__('mollie_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the instamojo configuration.
     */
    public function instamojoUpdate(Request $request)
    {
        $request->validate(
            [
                'im_key' => 'required',
                'im_secret' => 'required',
            ],
            [
                'im_key.required' => 'Instamojo Key is required',
                'im_secret.required' => 'Instamojo Secret is required',
            ],
        );

        try {
            checkSetEnv('IM_API_KEY', $request->im_key);
            checkSetEnv('IM_AUTH_TOKEN', $request->im_secret);
            setEnv('IM_ACTIVE', $request->instamojo ? 'true' : 'false');

            flashSuccess(__('instamojo_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manualPayment()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $manual_payment_gateways = ManualPayment::all();

            return view('backend.settings.pages.payment-manual', compact('manual_payment_gateways'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function manualPaymentStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        try {
            ManualPayment::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            flashSuccess(__('manual_payment_created_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function manualPaymentEdit(ManualPayment $manual_payment)
    {
        try {
            $manual_payment_gateways = ManualPayment::all();

            return view('backend.settings.pages.payment-manual', compact('manual_payment_gateways', 'manual_payment'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function manualPaymentUpdate(Request $request, ManualPayment $manual_payment)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        try {
            $manual_payment->update([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            flashSuccess(__('manual_payment_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function manualPaymentDelete(ManualPayment $manual_payment)
    {
        try {
            $manual_payment->delete();

            flashSuccess(__('manual_payment_deleted_successfully'));

            return redirect()->route('settings.payment.manual');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the manual payment status.
     */
    public function manualPaymentStatus(Request $request)
    {
        try {
            $manual_payment = ManualPayment::findOrFail($request->id);
            $manual_payment->update(['status' => $request->status]);

            return response()->json(['message' => __('payment_status_updated_successfully')]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
