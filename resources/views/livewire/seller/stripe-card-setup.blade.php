<div>
    <div id="card-element" class="mb-4 p-3 border border-gray-300 rounded"></div>
    <button id="card-button" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);">Save Card</button>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // document.addEventListener('livewire:load', function () {
            const stripe = Stripe("{{ config('services.stripe.key') }}");
            const elements = stripe.elements();
            const card = elements.create('card');
            card.mount('#card-element');

            const cardButton = document.getElementById('card-button');
            const clientSecret = @json($clientSecret);

            cardButton.addEventListener('click', async () => {
                const { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: {
                                name: "{{ auth()->user()->name }}",
                                email: "{{ auth()->user()->email }}"
                            },
                        },
                    }
                );

                if (error) {
                    alert(error.message);
                } else {
                    // Save to backend
                    fetch('/store-payment-method', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            payment_method: setupIntent.payment_method
                        })
                    }).then(() => {
                        alert('Card saved successfully!');
                        location.reload();
                    });
                }
            });
        // });
    </script>
</div>
