import './bootstrap';
import 'flowbite';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart button elements
    const cartButton = document.getElementById('cartButton');
    const itemsCount = document.getElementById('itemsCount');
    const itemsPrice = document.getElementById('itemsPrice');
    const itemsCard = document.getElementById('itemsCard');

    // Menambahkan event listener untuk tombol update quantity
    

    // Add click event listeners to all add-to-cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);

            // Send POST request to add item to cart
            fetch('/add-to-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf_token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    price: price
                })
            })
            .then(response => response.json())
            .then(data => {
                // Update cart display
                updateCartDisplay(data);
                
                // Show success feedback
                button.innerHTML = 'Added!';
                setTimeout(() => {
                    button.innerHTML = `Add
                        <svg class="w-6 h-6 ms-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                        </svg>`;
                }, 1000);
            })
            .catch(error => {
                console.error('Error:', error);
                button.innerHTML = 'Error';
                setTimeout(() => {
                    button.innerHTML = `Add
                        <svg class="w-6 h-6 ms-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                        </svg>`;
                }, 1000);
            });
        });
    });

    // Function to update cart display
    function updateCartDisplay(data) {
        // Show cart button if hidden
        cartButton.classList.remove('hidden');
        itemsCard.classList.add('mb-16');
        
        // Update items count
        itemsCount.textContent = `${data.itemCount} Items`;
        
        // Format and update total price
        const formattedPrice = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(data.totalPrice).replace('IDR', 'Rp.');
        
        itemsPrice.textContent = formattedPrice;
    }

    // Initialize cart display if there's existing cart data
    if (cartButton && !cartButton.classList.contains('hidden')) {
        // You might want to make an initial request to get cart data
        fetch('/get-cart-data')
            .then(response => response.json())
            .then(data => updateCartDisplay(data))
            .catch(error => console.error('Error loading cart data:', error));
    }
});
