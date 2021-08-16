<?php

namespace App\Reactors\Billing;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Models\Data\Reports;
use App\StorableEvents\BillingImportWrapped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class AWSBillingHistoryReactor extends Reactor implements ShouldQueue
{
    public function onBillingImportWrapped(BillingImportWrapped $event)
    {
        // For now, text Angel that its done.
        $msg = 'The AWS Billing Cron has completed.';
        FireTwilioMsg::run('2524129013', $msg);
        /**
         * STEPS
         * 1.
         * @todo - implement a communication subscription system
         * 2. Get the users who are online and push using redis
         * 3. Get the users not in list 2, who want to be texted and fire
         * 4. Get the users not in list 2, who want to be emailed and fire
         */
    }
}
