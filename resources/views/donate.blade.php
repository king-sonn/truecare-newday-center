@extends('layouts.app')

@section('content')
<!-- Donate Section Start -->
<section class="donate-section section-padding fix">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 wow fadeInUp" data-wow-delay=".2s">
                <!-- Hero -->
                <div class="donate-hero text-center mb-5">
                    <img src="{{ asset('images/hero-donate.jpg') }}" alt="Truecare Newday Center Donate" class="donate-hero-img w-100 rounded shadow">
                    <div class="donate-hero-overlay mt-4">
                        <span class="badge bg-teal text-white fs-6 fw-bold">Give a New Day</span>
                        <h2 class="mt-3 fw-bold text-dark">Support Teen Mothers</h2>
                        <p class="text-muted">Every contribution gives a young mother shelter, education, counseling and childcare.</p>
                    </div>
                </div>

                <!-- Paybill Info Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="paybill-card text-center p-3 rounded border bg-soft-cream">
                            <span class="d-block text-teal fw-bold small">Paybill</span>
                            <span class="d-block fs-4 fw-primary">247247</span>
                            <button class="copy-btn btn btn-sm btn-outline-teal mt-2" data-copy="247247">Copy</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="paybill-card text-center p-3 rounded border bg-soft-cream">
                            <span class="d-block text-teal fw-bold small">Account No</span>
                            <span class="d-block fs-4 fw-primary">958526</span>
                            <button class="copy-btn btn btn-sm btn-outline-teal mt-2" data-copy="958526">Copy</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="paybill-card text-center p-3 rounded border bg-soft-cream">
                            <span class="d-block text-teal fw-bold small">Account Name</span>
                            <span class="d-block fs-5 fw-primary">Truecare new day center</span>
                            <button class="copy-btn btn btn-sm btn-outline-teal mt-2" data-copy="Truecare new day center">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Donation Form -->
                <form id="mpesaForm" novalidate class="donate-form bg-white p-4 rounded shadow border">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="tel" name="phone" id="donate-phone" class="form-control form-control-lg"
                            placeholder="254720525984" pattern="2547[0-9]{7}"
                            value="254720525984" required autocomplete="tel">
                        <div class="form-text">Use format 2547XXXXXXXX (e.g. 254720525984)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (KES)</label>
                        <div class="amount-buttons mb-2">
                            <button type="button" class="amount-btn btn btn-outline-teal btn-sm rounded-pill" data-amount="500">500</button>
                            <button type="button" class="amount-btn btn btn-outline-teal btn-sm rounded-pill" data-amount="1000">1000</button>
                            <button type="button" class="amount-btn btn btn-outline-teal btn-sm rounded-pill" data-amount="5000">5000</button>
                            <button type="button" class="amount-btn btn btn-outline-teal btn-sm rounded-pill" data-amount="10000">10000</button>
                            <span class="text-muted small ms-2">KES</span>
                        </div>
                        <input type="number" name="amount" id="donate-amount" class="form-control form-control-lg"
                            placeholder="Enter amount" min="1" max="150000" value="1000" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="anonymous" id="donate-anonymous" class="form-check-input">
                        <label class="form-check-label" for="donate-anonymous">Make this donation anonymous</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message (optional)</label>
                        <textarea name="message" id="donate-message" class="form-control" rows="2"
                            placeholder="A message of encouragement for the young mothers..."></textarea>
                    </div>

                    <button type="submit" id="donate-submit" class="btn donate-btn w-100 py-3 fw-bold">
                        Donate Now
                    </button>
                </form>

                <!-- Manual Paybill Instructions -->
                <div class="manual-instructions mt-4 p-3 rounded bg-light border">
                    <h6 class="fw-bold text-teal">Pay Manually via M-Pesa</h6>
                    <p class="mb-2 small">Go to M-Pesa &rarr; Lipa na M-Pesa &rarr; Paybill</h6>
                    <p class="mb-0"><strong>Paybill:</strong> 247247</p>
                    <p class="mb-0"><strong>Account No:</strong> 958526</p>
                    <p class="mb-0"><strong>Account Name:</strong> Truecare new day center</p>
                </div>
            </div>
        </div>
    </div>
</section>
</section>

<!-- Loader / Status Modal -->
<div class="modal fade" id="donateLoaderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-teal mb-3" role="status"></div>
                <h5 id="loaderTitle" class="fw-bold">Processing Payment</h5>
                <p id="loaderMessage" class="text-muted mb-0">Check phone &amp; Enter M-Pesa PIN to pay to 247247 / 958526</p>
            </div>
        </div>
    </div>
</div>

<!-- Success / Thank You Modal -->
<div class="modal fade" id="donateSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3 shadow">
            <div class="modal-body text-center p-5 bg-success-subtle rounded-3">
                <div class="celebration-icon mb-4">&#127881;</div>
                <h3 id="successTitle" class="fw-bold text-success">Asante Sana!</h3>
                <p id="successMessage" class="text-muted mb-4"></p>
                <div class="impact-card p-3 rounded bg-white mb-4 border">
                    <h6 class="fw-bold text-dark mb-2">Your Impact</h6>
                    <p class="small mb-0" id="successImpact">You gave a new day to a young mother in Kabiria.</p>
                </div>
                <button class="btn btn-outline-teal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Failure Modal -->
<div class="modal fade" id="donateFailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 shadow">
            <div class="modal-body text-center p-4 bg-danger-subtle rounded-3">
                <div class="mb-3">&#128531;</div>
                <h5 id="failTitle" class="fw-bold text-danger">Payment Failed</h5>
                <p id="failMessage" class="text-muted mb-4"></p>
                <div class="text-start mb-4">
                    <h6 class="fw-bold text-dark">Pay manually via M-Pesa:</h6>
                    <p class="small mb-1"><strong>Paybill:</strong> <span class="copyable">247247</span></p>
                    <p class="small mb-1"><strong>Account No:</strong> <span class="copyable">958526</span></p>
                    <p class="small mb-0"><strong>Account Name:</strong> Truecare new day center</p>
                </div>
                <button class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@endsection

@push('styles')
<style>
    :root{
        --teal:#0F766E;
        --coral:#FF6B6B;
        --cream:#FFFBEB;
        --yellow:#FBBF24;
    }
    .text-teal{color:var(--teal)!important;}
    .bg-teal{background-color:var(--teal)!important;}
    .bg-soft-cream{background-color:var(--cream)!important;}
    .btn-outline-teal{
        color:var(--teal);
        border-color:var(--teal);
    }
    .btn-outline-teal:hover{
        background-color:var(--teal);
        color:#fff;
    }
    .donate-btn{
        background-color:var(--teal);
        color:#fff;
        border:none;
    }
    .donate-btn:hover{
        background-color:var(--coral);
        color:#fff;
    }
    .donate-hero-img{
        max-height:320px;
        object-fit:cover;
        border-radius:16px;
    }
    .donate-hero-overlay{
        background:linear-gradient(135deg, rgba(15,118,110,0.6), rgba(255,107,107,0.6));
        color:#fff;
        padding:2rem;
        border-radius:16px;
        margin-top:-60px;
        position:relative;
        z-index:2;
        box-shadow:0 8px 32px rgba(0,0,0,0.15);
    }
    .donate-hero-overlay h2{color:#fff;}
    .donate-hero-overlay p{color:rgba(255,255,255,0.9);}
    .paybill-card{
        background:var(--cream);
        border:1px solid rgba(15,118,110,0.2);
    }
    .amount-btn{
        transition:all 0.2s;
    }
    .amount-btn.active,
    .amount-btn.selected{
        background-color:var(--teal);
        border-color:var(--teal);
        color:#fff;
    }
    .copy-btn{
        font-size:0.75rem;
        padding:0.2rem 0.5rem;
    }
    .copyable{
        font-family:monospace;
        font-weight:bold;
        color:var(--teal);
        cursor:pointer;
        text-decoration:underline;
    }
    .copyable:hover{color:var(--coral);}
    .celebration-icon{font-size:3rem;}
    .impact-card{
        border:1px solid var(--teal);
        background:var(--cream);
    }
    .manual-instructions{
        background:#f8f9fa;
        border:1px dashed #dee2e6;
    }
</style>
@endpush
@push('scripts')
<script>
(function(){
    const form = document.getElementById('mpesaForm');
    const phoneInput = document.getElementById('donate-phone');
    const amountInput = document.getElementById('donate-amount');
    const submitBtn = document.getElementById('donate-submit');
    const amountBtns = document.querySelectorAll('.amount-btn');
    const loaderModal = new bootstrap.Modal(document.getElementById('donateLoaderModal'));
    const successModal = new bootstrap.Modal(document.getElementById('donateSuccessModal'));
    const failModal = new bootstrap.Modal(document.getElementById('donateFailModal'));
    const loaderTitle = document.getElementById('loaderTitle');
    const loaderMessage = document.getElementById('loaderMessage');
    const successTitle = document.getElementById('successTitle');
    const successMessage = document.getElementById('successMessage');
    const successImpact = document.getElementById('successImpact');
    const failTitle = document.getElementById('failTitle');
    const failMessage = document.getElementById('failMessage');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const pushRoute = '{{ route("donate.mpesa.push") }}';
    const statusRouteBase = '{{ route("donate.status", "0") }}'.replace('/0', '');

    // Amount quick buttons
    amountBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            amountBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            amountInput.value = btn.dataset.amount;
        });
    });

    // Copy buttons
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const text = btn.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const orig = btn.innerText;
                btn.innerText = 'Copied!';
                setTimeout(() => btn.innerText = orig, 1200);
            });
        });
    });
    document.querySelectorAll('.copyable').forEach(el => {
        el.addEventListener('click', () => {
            navigator.clipboard.writeText(el.innerText);
        });
    });

    function showLoader(msg){
        loaderMessage.innerText = msg || 'Check phone & Enter M-Pesa PIN to pay to 247247 / 958526';
        loaderModal.show();
    }
    function hideLoader(){
        loaderModal.hide();
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const phone = phoneInput.value.trim();
        const amount = amountInput.value.trim();

        if (!/^254[71][0-9]{8}$/.test(phone)) {
            alert('Please enter a valid phone number (e.g. 254720525984)');
            return;
        }
        if (!amount || parseFloat(amount) < 1) {
            alert('Please enter a valid amount');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerText = 'Processing...';
        showLoader('Check phone & Enter M-Pesa PIN to pay to 247247 / 958526');

        try {
            const resp = await fetch(pushRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ phone, amount })
            });

            const data = await resp.json();

            if (data.success && data.checkout_id) {
                hideLoader();
                pollStatus(data.checkout_id);
            } else {
                hideLoader();
                submitBtn.disabled = false;
                submitBtn.innerText = 'Donate Now';
                failTitle.innerText = 'Payment Failed';
                failMessage.innerText = data.message || 'Unable to initiate payment. Please try again.';
                failModal.show();
            }
        } catch (err) {
            hideLoader();
            submitBtn.disabled = false;
            submitBtn.innerText = 'Donate Now';
            failTitle.innerText = 'Error';
            failMessage.innerText = err.message || 'Something went wrong.';
            failModal.show();
        }
    });

    async function pollStatus(checkoutId){
        let attempts = 0;
        const maxAttempts = 20;

        async function tick(){
            attempts++;
            try {
                const resp = await fetch(statusRouteBase.replace('{checkoutId}', checkoutId), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await resp.json();

                if (data.success) {
                    if (data.status === 'completed') {
                        hideLoader();
                        successTitle.innerText = 'Asante Sana!';
                        successMessage.innerHTML = `Thank you for your contribution of <strong>Ksh ${data.amount}</strong> to Truecare Newday Center.`;
                        successImpact.innerText = `You gave a new day to a young mother in Kabiria.`;
                        successModal.show();
                        return;
                    }
                    if (data.status === 'failed') {
                        hideLoader();
                        failTitle.innerText = 'Payment Failed';
                        failMessage.innerText = data.result_desc || 'The transaction did not complete.';
                        failModal.show();
                        return;
                    }
                    if (attempts < maxAttempts) {
                        setTimeout(tick, 3000);
                    } else {
                        hideLoader();
                        failTitle.innerText = 'Payment Pending';
                        failMessage.innerText = 'Your payment is still being processed. Please check your phone or try again.';
                        failModal.show();
                    }
                } else {
                    hideLoader();
                    failTitle.innerText = 'Status Check Failed';
                    failMessage.innerText = data.message || 'Unable to verify payment status.';
                    failModal.show();
                }
            } catch (err) {
                if (attempts < maxAttempts) {
                    setTimeout(tick, 3000);
                } else {
                    hideLoader();
                    failTitle.innerText = 'Error';
                    failMessage.innerText = err.message || 'Something went wrong.';
                    failModal.show();
                }
            }
        }

        tick();
    }
})();
</script>
@endpush