@extends('frontend.layouts.plain')

@push('styles')
	<style type="text/css">
		.payment-receipt-title{color:#222;font:normal 24px Arial,Helvetica,sans-serif;line-height:33px;display:inline-block;vertical-align:middle}
		.payment-receipt{max-width:680px;font:normal 13px Arial,Helvetica,sans-serif}.payment-receipt-section{margin-top:16px}
		.payment-receipt-section dd{margin:0}
		.payment-receipt-logo,
		.payment-receipt-address-info,
		.payment-receipt-label{display:inline-block;width:45%}
		.payment-receipt-logo img{vertical-align:middle;width: 65%;}
		.payment-receipt-label{color:#222;font:bold 13px Arial,Helvetica,sans-serif;line-height:18px}
		.payment-receipt-opined-image{vertical-align:middle}
		.payment-receipt-payment-info,
		.payment-receipt-value{display:inline-block;margin:0;vertical-align:top;width:50%}
		.payment-receipt-details-table{width:100%;font:normal 13px Arial,Helvetica,sans-serif}
		.payment-receipt-details-table,
		.payment-receipt-details-table td,
		.payment-receipt-details-table th{border:1px solid black;border-collapse:collapse;padding:2px 6px}
		.payment-receipt-details-table th{color:#222;font:bold 13px Arial,Helvetica,sans-serif;line-height:18px;text-align:left}
		.payment-receipt-wu-section>dt{border-bottom:1px solid black;margin-bottom:6px;width:100%}
		.payment-receipt-wu-section>dd{margin-left:6px}
	.payment-address-line{display:block}</style>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush
@push('scripts')
@endpush

@section('content')
	<div class="payment-receipt">
		<div class="payment-receipt-section">
			<div class="payment-receipt-logo">
				<img src="/img/logo.png" alt="Opined logo" class="logo-img">
			</div>
			<h1 class="payment-receipt-title">Payment Receipt</h1> 
			<button onclick="window.print()" style="margin-left: 22%; cursor: pointer"><i class="fa fa-print"></i></button>
		</div>
		<div class="payment-receipt-section">
			<div class="payment-receipt-address-info">
				<div class="payment-receipt-section payment-receipt-opined-address">
					<span class="payment-address-line">Opined Online Business Services Pvt Ltd</span>
					<span class="payment-address-line">Mumbai</span>
					<span class="payment-address-line">India</span>
					
				</div>
				<!--<div class="payment-receipt-section">
					<dl>
						<dt>Tax identification number</dt>
						<dd class="tax-id-number">200817984R</dd>
					</dl>
				</div>-->
				<div class="payment-receipt-section payment-receipt-customer-address">
					<span class="payment-address-line">{{$user_invoice->user['name']}}</span>
					<span class="payment-address-line">{{$user_invoice->user_account['address']}}</span>
					<span class="payment-address-line">{{$user_invoice->user_account['city']}}</span>
					<span class="payment-address-line">{{$user_invoice->user_account['state']}}</span>
					<span class="payment-address-line">{{$user_invoice->user_account['country']}},{{$user_invoice->user_account['zip_code']}}</span>
				</div>
			</div>
			<dl class="payment-receipt-payment-info payment-receipt-section">
				<dt class="payment-receipt-label">Payment date</dt>
				<dd class="payment-receipt-value">{{date("M d, Y", strtotime($user_invoice->created_at))}}</dd>
				<dt class="payment-receipt-label">Billing ID</dt>
				<dd class="payment-receipt-value">{{$user_invoice->billing_id}}</dd>
				<dt class="payment-receipt-label">Payment method</dt>
				@if($user_invoice->user['phone_code']=="+91")
				<dd class="payment-receipt-value">Wire transfer to bank account&nbsp;&nbsp;• • • • {{substr($user_invoice->user_account['account_no'], -4)}}</dd>
				@else
				<dd class="payment-receipt-value">Paypal account with user&nbsp;&nbsp;• • • • {{substr($user_invoice->user['email'], -12)}}</dd>
				@endif
				<dt class="payment-receipt-label">Payment number</dt>
				<dd class="payment-receipt-payment-number payment-receipt-value">{{$user_invoice->payment_refrence_number}}</dd>
			</dl>
		</div>
		<table class="payment-receipt-section payment-receipt-details-table">
			<tbody>
				<tr>
					<th>Description</th>
					<th>Ammount (USD)</th>
				</tr>
				<tr>
					<td>Payment amount</td>
					<td class="payment-receipt-amount">${{$user_invoice->paid}}</td>
				</tr>
			</tbody>
		</table>
	</div>

@endsection