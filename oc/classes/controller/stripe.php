<?php defined('SYSPATH') or die('No direct script access.');

/**
* Stripe class
*
* @package Open Classifieds
* @subpackage Core
* @category Helper
* @author Chema Garrido <chema@open-classifieds.com>
* @license GPL v3
*/

class Controller_Stripe extends Controller{
    
    /**
     * [action_form] generates the form to pay at paypal
     */
    public function action_pay()
    { 
        $this->auto_render = FALSE;

        $id_order = $this->request->param('id');

        //retrieve info for the item in DB
        $order = new Model_Order();
        $order = $order->where('id_order', '=', $id_order)
                       ->where('status', '=', Model_Order::STATUS_CREATED)
                       ->limit(1)->find();

        if ($order->loaded())
        {

            if ( isset( $_POST[ 'stripeToken' ] ) ) 
            {
                // include class vendor
                require Kohana::find_file('vendor/stripe/lib', 'Stripe');

                // Set your secret key: remember to change this to your live secret key in production
                // See your keys here https://manage.stripe.com/account
                Stripe::setApiKey(Core::config('payment.stripe_private'));

                // Get the credit card details submitted by the form
                $token = Core::post('stripeToken');

                // email
                $email = Core::post('stripeEmail');

                // Create the charge on Stripe's servers - this will charge the user's card
                try 
                {
                    $charge = Stripe_Charge::create(array(
                                                        "amount"    => StripeKO::money_format($order->amount), // amount in cents, again
                                                        "currency"  => $order->currency,
                                                        "card"      => $token,
                                                        "description" => $order->description)
                                                    );

                    if (!Auth::instance()->logged_in())
                    {
                        //create user if doesnt exists and send email to user with password
                        $user = Model_User::create_email($email,core::post('stripeBillingName',$email));
                    }
                    else//he was loged so we use his user
                        $user = Auth::instance()->get_user();

                    //mark as paid
                    $order->confirm_payment('stripe',Core::post('stripeToken'));
                    
                    //redirect him to his ads
                    Alert::set(Alert::SUCCESS, __('Thanks for your payment!'));
                    $this->redirect(Route::url('oc-panel', array('controller'=>'profile','action'=>'orders')));
                    
                    
                }
                catch(Stripe_CardError $e) 
                {
                    // The card has been declined
                    Kohana::$log->add(Log::ERROR, 'Stripe The card has been declined');
                    Alert::set(Alert::ERROR, 'Stripe The card has been declined');
                    $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
                }
                
            }
            else
            {
                Alert::set(Alert::INFO, __('Please fill your card details.'));
                $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
            }
            
        }
        else
        {
            Alert::set(Alert::INFO, __('Order could not be loaded'));
            $this->redirect(Route::url('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order)));
        }
    }


}