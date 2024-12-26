<?php

namespace App\Http\Controllers\V1\Guest;

use App\Http\Controllers\Controller;
/* 新增收款显示余额 */
use App\Models\Plan;
use App\Models\User;
/* 新增收款显示余额 结束 */
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\TelegramService;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    public function notify($method, $uuid, Request $request)
    {
        try {
            $paymentService = new PaymentService($method, null, $uuid);
            $verify = $paymentService->notify($request->input());
            if (!$verify) abort(500, 'verify error');
            if (!$this->handle($verify['trade_no'], $verify['callback_no'])) {
                abort(500, 'handle error');
            }
            return(isset($verify['custom_result']) ? $verify['custom_result'] : 'success');
        } catch (\Exception $e) {
            abort(500, 'fail');
        }
    }

    private function handle($tradeNo, $callbackNo)
    {
        $order = Order::where('trade_no', $tradeNo)->first();
        if (!$order) {
            abort(500, 'order is not found');
        }
        if ($order->status !== 0) return true;
        $orderService = new OrderService($order);
        if (!$orderService->paid($callbackNo)) {
            return false;
        }
        /* 新增收款显示余额 */
        $dayIncome = Order::where('created_at', '>=', strtotime(date('Y-m-d')))
            ->where('created_at', '<', time())
            ->whereNotIn('status', [0, 2])
            ->sum('total_amount');
        $plan = Plan::find($order->plan_id);
        /* 新增收款显示余额 结束 */
        $telegramService = new TelegramService();
        // $message = sprintf(
        //     "💰成功收款%s元\n———————————————\n订单号：%s",
        //     $order->total_amount / 100,
        //     $order->trade_no
        // );
        $message = sprintf(
            "💰成功收款%s元\n———————————————\n订单号：%s\n套餐：%s\n———————————————\n当日总计流水：%s",
            $order->total_amount / 100,
            // $order->trade_no, $plan->name, $dayIncome
            $order->trade_no, $plan->name, $dayIncome/100 
        ); /* 新增收款显示余额 */
        $telegramService->sendMessageWithAdmin($message);
        return true;
    }
}
