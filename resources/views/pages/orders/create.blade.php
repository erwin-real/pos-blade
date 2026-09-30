<x-app-layout>

    <x-slot:topbarTitle>
        Point of sale
    </x-slot>

    <div class="body-right">
        <div class="container-fluid py-3" style="max-height: calc(100vh - 100px);">
            <div class="row h-100">
                
                <!-- LEFT PANEL: Product Selection -->
                <div class="col-lg-7 col-xl-8 d-flex flex-column h-100" style="overflow-y: auto; max-height: calc(100vh - 120px);">
                    <!-- Search and Category Filters -->
                    <div class="card shadow mb-3">
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-md-5 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <input type="text" id="product-search" class="form-control" placeholder="Search by Name or description...">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 d-flex overflow-auto pb-1" style="white-space: nowrap; gap: 5px;">
                                    <!-- Added 'btn-category' class and data-category-id -->
                                    <button class="btn btn-primary btn-sm px-3 btn-category active" data-category-id="all">All</button>
                                    @foreach($categories as $category)
                                        <button class="btn btn-outline-primary btn-sm px-3 btn-category" data-category-id="{{ $category->id }}">{{ $category->name }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid Grid -->
                    <div class="row" id="products-container">
                        @foreach($products as $product)
                        <div class="col-6 col-sm-4 col-md-3 mb-3 product-item" data-category="{{ $product->category_id }}">
                            <!-- Using plain data fields for clean JS mapping -->
                            <div class="card h-100 shadow-sm border-0 btn p-0 text-left align-baseline position-relative product-card btn-add-to-cart" 
                                style="cursor: pointer; transition: transform 0.2s;"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-desc="{{ $product->desc }}"
                                data-srp="{{ $product->srp }}"
                                data-stocks="{{ $product->stocks }}">
                                
                                @if($product->stocks <= ($product->reorder_level ?? 10))
                                    <span class="badge badge-danger position-absolute" style="top: 5px; right: 5px; z-index: 10;">Low Stock</span>
                                @endif

                                <div class="card-body p-2 d-flex flex-column justify-content-between text-center">
                                    <div class="p-3 bg-light rounded mb-2">
                                        {{-- <i class="fas fa-box fa-2x text-gray-300"></i> --}}
                                    <img class="img-thumbnail rounded" src="/storage/products/{{$product->cover_image}}" alt="">
                                    </div>
                                    <div>
                                        <small class="text-xs font-weight-bold text-uppercase text-gray-500 d-block text-truncate">{{ $product->desc }}</small>
                                        <h6 class="font-weight-bold text-gray-800 text-truncate mb-1">{{ $product->name }}</h6>
                                    </div>
                                    <div class="text-primary font-weight-bold mt-1">
                                        ₱{{ number_format($product->srp, 2) }}
                                    </div>
                                    <small class="text-xs text-muted d-block mt-1">Stock: {{ $product->stocks }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- RIGHT PANEL: Order Summary / Cart -->
                <div class="col-lg-5 col-xl-4 d-flex flex-column h-100" style="max-height: calc(100vh - 120px);">
                    <div class="card shadow h-100 d-flex flex-column">
                        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart mr-2"></i>Current Order</h6>
                            <span class="badge badge-light px-2 py-1" id="cart-count">0 Items</span>
                        </div>

                        <div class="card-body p-0 flex-grow-1 overflow-auto" id="cart-items-wrapper" style="max-height: calc(100vh - 430px);">
                            <div id="empty-cart-msg" class="text-center py-5 text-gray-400">
                                <i class="fas fa-receipt fa-3x mb-3"></i>
                                <p>No products added to the order yet.</p>
                            </div>
                            
                            <table class="table table-align-middle m-0 d-none" id="cart-table">
                                <thead class="bg-light sticky-top">
                                    <tr class="text-xs text-gray-600 font-weight-bold uppercase border-0">
                                        <th class="border-0 pl-3">Item</th>
                                        <th class="border-0 text-center" style="width: 100px;">Qty</th>
                                        <th class="border-0 text-right pr-3" style="width: 100px;">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body">
                                    <!-- Injected dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-top p-3 mt-auto">
                            <div class="d-flex justify-content-between text-xs text-gray-600 mb-1">
                                <span>Subtotal</span>
                                <span id="summary-subtotal">₱0.00</span>
                            </div>
                            {{-- <div class="d-flex justify-content-between text-xs text-gray-600 mb-1">
                                <span>VAT (12%)</span>
                                <span id="summary-tax">₱0.00</span>
                            </div> --}}
                            {{-- <div class="d-flex justify-content-between text-xs text-gray-600 mb-2">
                                <span>Discount</span>
                                <span class="text-success" id="summary-discount">-₱0.00</span>
                            </div> --}}
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="font-weight-bold text-gray-800">Total Amount</span>
                                <h4 class="font-weight-bold text-danger m-0" id="summary-total">₱0.00</h4>
                            </div>

                            <div class="row g-2">
                                <div class="col-6 pr-1">
                                    <!-- Assigned direct native clear handler click trigger -->
                                    <button class="btn btn-outline-danger btn-block btn-sm" id="btn-clear-cart">
                                        <i class="fas fa-trash-alt mr-1"></i> Cancel
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button class="btn btn-success btn-block btn-sm font-weight-bold" data-toggle="modal" data-target="#paymentModal" id="checkout-btn" disabled>
                                        <i class="fas fa-money-bill-wave mr-1"></i> Pay (F8)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- CHECKOUT PAYMENT MODAL -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="paymentModalLabel">
                            <i class="fas fa-money-bill-wave mr-2"></i>Complete Transaction
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Transaction Summary Block -->
                        <div class="bg-light rounded p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1 text-muted">
                                <span>Total Items:</span>
                                <span id="modal-item-count" class="font-weight-bold text-gray-800">0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-gray-800">Amount Due:</span>
                                <h3 class="font-weight-bold text-danger m-0" id="modal-amount-due">₱0.00</h3>
                            </div>
                        </div>

                        <!-- Input Fields Form -->
                        <form id="payment-form" onsubmit="event.preventDefault();">
                            <div class="form-group mb-3">
                                <label for="payment-method" class="text-xs font-weight-bold text-uppercase text-gray-600">Payment Method</label>
                                <select class="form-control font-weight-bold" id="payment-method">
                                    <option value="Cash">💵 Cash</option>
                                    <option value="CreditCard">💳 Credit Card</option>
                                    <option value="GCash">📱 GCash</option>
                                    <option value="Maya">📱 Maya</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label for="cash-received" class="text-xs font-weight-bold text-uppercase text-gray-600">Cash Received (₱)</label>
                                <input type="number" 
                                    step="0.01" 
                                    class="form-control form-control-lg text-success font-weight-bold" 
                                    id="cash-received" 
                                    placeholder="0.00" 
                                    style="font-size: 1.5rem; height: calc(1.5em + 1rem + 2px);" 
                                    required 
                                    autocomplete="off">
                            </div>

                            <!-- Change Calculation Block -->
                            <div class="bg-gray-100 rounded p-3 d-flex justify-content-between align-items-center border">
                                <span class="font-weight-bold text-gray-700">Change Due:</span>
                                <h3 class="font-weight-bold text-success m-0" id="modal-change-due">₱0.00</h3>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success font-weight-bold px-4" id="btn-submit-order" disabled>
                            <i class="fas fa-check-circle mr-1"></i> Finalize Order
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <style>
            .product-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
            .table-align-middle td { vertical-align: middle !important; }
            .sticky-top { position: sticky; top: 0; z-index: 10; }
        </style>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let cart = [];
        // const taxRate = 0.12;
        let activeCategoryId = 'all';

        // Cache elements for high performance
        const searchInput = document.getElementById('product-search');
        const productItems = document.querySelectorAll('.product-item');
        const categoryButtons = document.querySelectorAll('.btn-category');
        const emptyCartMsg = document.getElementById('empty-cart-msg');
        const cartTable = document.getElementById('cart-table');
        const cartBody = document.getElementById('cart-body');
        const checkoutBtn = document.getElementById('checkout-btn');
        const clearCartBtn = document.getElementById('btn-clear-cart');

        // ==========================================
        // 1. ADD TO CART EVENT HANDLER
        // ==========================================
        document.querySelectorAll('.btn-add-to-cart').forEach(card => {
            card.addEventListener('click', function() {
                let product = {
                    id: parseInt(this.getAttribute('data-id')),
                    name: this.getAttribute('data-name'),
                    desc: this.getAttribute('data-desc'),
                    srp: parseFloat(this.getAttribute('data-srp')),
                    stocks: parseInt(this.getAttribute('data-stocks'))
                };
                processCartAddition(product);
            });
        });

        // ==========================================
        // 2. LIVE SEARCH INPUT MATCH
        // ==========================================
        searchInput.addEventListener('keyup', filterProducts);

        // ==========================================
        // 3. CATEGORY TOGGLE MANAGEMENT
        // ==========================================
        categoryButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                categoryButtons.forEach(b => {
                    b.classList.remove('btn-primary', 'active');
                    b.classList.add('btn-outline-primary');
                });

                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary', 'active');

                activeCategoryId = this.getAttribute('data-category-id');
                filterProducts();
            });
        });

        // ==========================================
        // COMBINED PRODUCT SEARCH & CATEGORY FILTER
        // ==========================================
        function filterProducts() {
            const searchVal = searchInput.value.toLowerCase().trim();

            productItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                const productName = item.querySelector('h6').textContent.toLowerCase();
                const productDesc = item.querySelector('small').textContent.toLowerCase();

                const matchesSearch = searchVal === "" || productName.includes(searchVal) || productDesc.includes(searchVal);
                const matchesCategory = activeCategoryId === 'all' || itemCategory === activeCategoryId;

                if (matchesSearch && matchesCategory) {
                    item.style.display = "";
                } else {
                    item.style.display = "none";
                }
            });
        }

        // ==========================================
        // CART ENGINE OPERATIONS
        // ==========================================
        function processCartAddition(product) {
            if (product.stocks <= 0) {
                alert('This product is out of stock!');
                return;
            }

            let existingItem = cart.find(item => item.id === product.id);

            if (existingItem) {
                if (existingItem.quantity >= product.stocks) {
                    alert('Cannot exceed available warehouse stock limit.');
                    return;
                }
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    desc: product.desc,
                    srp: product.srp,
                    quantity: 1,
                    max_stock: product.stocks
                });
            }
            renderCart();
        }

        // Expose utility controls globally to handle table inner loop strings securely
        window.updateQuantity = function(productId, newQty) {
            let item = cart.find(item => item.id === productId);
            if (!item) return;

            newQty = parseInt(newQty);

            if (newQty <= 0 || isNaN(newQty)) {
                removeFromCart(productId);
                return;
            }

            if (newQty > item.max_stock) {
                alert('Cannot exceed available warehouse stock limit.');
                item.quantity = item.max_stock;
            } else {
                item.quantity = newQty;
            }
            renderCart();
        };

        window.removeFromCart = function(productId) {
            cart = cart.filter(item => item.id !== productId);
            renderCart();
        };

        clearCartBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to void this current order transaction?')) {
                cart = [];
                renderCart();
            }
        });

        // ==========================================
        // VIEW RENDERING LOOPS
        // ==========================================
        function renderCart() {
            cartBody.innerHTML = "";

            if (cart.length === 0) {
                emptyCartMsg.classList.remove('d-none');
                cartTable.classList.add('d-none');
                checkoutBtn.setAttribute('disabled', 'true');
                document.getElementById('cart-count').textContent = '0 Items';
                document.getElementById('summary-subtotal').textContent = '₱0.00';
                // document.getElementById('summary-tax').textContent = '₱0.00';
                document.getElementById('summary-total').textContent = '₱0.00';
                return;
            }

            emptyCartMsg.classList.add('d-none');
            cartTable.classList.remove('d-none');
            checkoutBtn.removeAttribute('disabled');

            let subtotal = 0;
            let totalItemsCount = 0;

            cart.forEach(item => {
                let itemSubtotal = item.srp * item.quantity;
                subtotal += itemSubtotal;
                totalItemsCount += item.quantity;

                let rowHtml = `
                    <tr class="border-bottom">
                        <td class="pl-3 py-2">
                            <span class="font-weight-bold text-gray-800 d-block text-truncate" style="max-width: 150px;">${item.name}</span>
                            <small class="text-muted d-block text-xs">${item.desc}</small>
                        </td>
                        <td class="text-center py-2">
                            <div class="input-group input-group-sm m-auto" style="width: 90px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary btn-sm px-2" onclick="window.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                </div>
                                <input type="text" class="form-control text-center p-0 font-weight-bold" value="${item.quantity}" onchange="window.updateQuantity(${item.id}, this.value)">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-sm px-2" onclick="window.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                </div>
                            </div>
                        </td>
                        <td class="text-right pr-3 py-2 font-weight-bold text-gray-700">
                            ₱${itemSubtotal.toFixed(2)}
                            <button class="btn btn-sm btn-link text-danger p-0 ml-1 align-middle" onclick="window.removeFromCart(${item.id})" title="Remove">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </td>
                    </tr>
                `;
                cartBody.insertAdjacentHTML('beforeend', rowHtml);
            });

            // let calculatedTax = subtotal * taxRate;
            // let totalAmount = subtotal + calculatedTax;
            let totalAmount = subtotal;

            document.getElementById('cart-count').textContent = `${totalItemsCount} ${totalItemsCount === 1 ? 'Item' : 'Items'}`;
            document.getElementById('summary-subtotal').textContent = `₱${subtotal.toFixed(2)}`;
            // document.getElementById('summary-tax').textContent = `₱${calculatedTax.toFixed(2)}`;
            document.getElementById('summary-total').textContent = `₱${totalAmount.toFixed(2)}`;
        }

        // Hotkey hooks
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F8') {
                e.preventDefault();
                if (!checkoutBtn.hasAttribute('disabled')) {
                    checkoutBtn.click();
                }
            }
        });

        let totalPayableAmount = 0; 

        // Cache New Modal DOM Elements
        const paymentModal = document.getElementById('paymentModal');
        const cashReceivedInput = document.getElementById('cash-received');
        const modalAmountDue = document.getElementById('modal-amount-due');
        const modalItemCount = document.getElementById('modal-item-count');
        const modalChangeDue = document.getElementById('modal-change-due');
        const submitOrderBtn = document.getElementById('btn-submit-order');
        const paymentMethodSelect = document.getElementById('payment-method');

        document.getElementById('modal-item-count').textContent = 32;
        // ==========================================
        // 1. BOOTSTRAP MODAL OPEN LISTENER
        // ==========================================
        // Listens via native jQuery events triggered by Bootstrap 4
        $('#paymentModal').on('show.bs.modal', function () {
            // Calculate items and total sum from global cart state tracking arrays
            let subtotal = 0;
            let totalItemsCount = 0;
            
            cart.forEach(item => {
                subtotal += item.srp * item.quantity;
                totalItemsCount += item.quantity;
            });

            // let calculatedTax = subtotal * taxRate;
            // totalPayableAmount = subtotal + calculatedTax;
            totalPayableAmount = subtotal;

            // Map values into display components
            modalItemCount.textContent = totalItemsCount;
            modalAmountDue.textContent = `₱${totalPayableAmount.toFixed(2)}`;
            
            // Reset inputs and values
            cashReceivedInput.value = '';
            modalChangeDue.textContent = '₱0.00';
            modalChangeDue.className = "font-weight-bold text-success m-0";
            submitOrderBtn.setAttribute('disabled', 'true');

            // Automatically focus cash field for immediate typing
            setTimeout(() => cashReceivedInput.focus(), 500);
        });

        // ==========================================
        // 2. LIVE CASH INPUT CHANGE ENGINE
        // ==========================================
        cashReceivedInput.addEventListener('input', calculateChange);
        paymentMethodSelect.addEventListener('change', handlePaymentMethodChange);

        function handlePaymentMethodChange() {
            // If non-cash, auto-fill full payment parameters for faster processing
            if (paymentMethodSelect.value !== 'Cash') {
                cashReceivedInput.value = totalPayableAmount.toFixed(2);
                cashReceivedInput.setAttribute('readonly', 'true');
            } else {
                cashReceivedInput.value = '';
                cashReceivedInput.removeAttribute('readonly');
            }
            calculateChange();
        }

        function calculateChange() {
            const rawCash = parseFloat(cashReceivedInput.value);
            const cashReceived = isNaN(rawCash) ? 0 : rawCash;
            const changeDue = cashReceived - totalPayableAmount;

            if (cashReceived === 0) {
                modalChangeDue.textContent = '₱0.00';
                modalChangeDue.className = "font-weight-bold text-success m-0";
                submitOrderBtn.setAttribute('disabled', 'true');
                return;
            }

            if (changeDue >= 0) {
                // Cash covers total payment parameters
                modalChangeDue.textContent = `₱${changeDue.toFixed(2)}`;
                modalChangeDue.className = "font-weight-bold text-success m-0";
                submitOrderBtn.removeAttribute('disabled');
            } else {
                // Insufficient cash balance indicator
                modalChangeDue.textContent = `Short: ₱${Math.abs(changeDue).toFixed(2)}`;
                modalChangeDue.className = "font-weight-bold text-danger m-0";
                submitOrderBtn.setAttribute('disabled', 'true');
            }
        }

        // ==========================================
        // 3. FINALIZE SUBMIT OPERATIONS
        // ==========================================
        submitOrderBtn.addEventListener('click', function() {
            submitOrderBtn.setAttribute('disabled', 'true');
            
            // 1. Map dynamic transactional arrays
            const orderPayload = {
                payment_method: paymentMethodSelect.value,
                amount_paid: parseFloat(cashReceivedInput.value),
                change: modalChangeDue.innerText.slice(1),
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    srp: item.srp
                }))
            };
            console.log("orderPayload: ", orderPayload);

            // 2. Fetch the active CSRF verification token string
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // 3. Dispatch the payload asynchronously to Laravel 13 backend routing tables
            fetch('/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(orderPayload)
            })
            .then(response => {
                if (!response.ok) {
                    // Forward HTTP exception details directly to catcher block rules
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message || 'Transaction processed successfully!');
                
                // Hide modal & clear global session states
                cart = [];
                renderCart();
                
                // let modalInstance = bootstrap.Modal.getInstance(paymentModal);
                // if (modalInstance) { modalInstance.hide(); } else { $('#paymentModal').modal('hide'); }
                
                // Reload page to reflect updated database product stock numbers
                window.location.reload();
            })
            .catch(error => {
                console.error('POS System Error:', error);
                alert('Transaction Failed: ' + (error.message || 'Server error occurred processing the cart.'));
                submitOrderBtn.removeAttribute('disabled'); // Re-enable for cashier correction
            });
        });

    });

    

</script>


</x-app-layout>
