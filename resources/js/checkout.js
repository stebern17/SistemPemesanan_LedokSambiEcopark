import "./bootstrap";
import "flowbite";


function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk menghitung total harga
    function calculateTotalPrice() {
        let totalPrice = 0;
        const cartItems = document.querySelectorAll('.grid.grid-cols-2');

        cartItems.forEach(item => {
            const priceElement = item.querySelector('p'); // Mengambil elemen harga
            const quantityInput = item.querySelector('input[data-input-counter]'); // Mengambil elemen input quantity
            const price = parseFloat(priceElement.innerText.replace('Rp. ', '').replace(/\./g, '').trim()); // Mengambil harga dan menghapus format
            const quantity = parseInt(quantityInput.value); // Mengambil nilai quantity

            if (quantity <= 0) {
                // Jika jumlah item 0 atau kurang, hapus item dari DOM
                item.remove();
                fetch('/remove-from-cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf_token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: item.dataset.name,
                    })
                })
            } else {
                // Menghitung total harga untuk item ini
                totalPrice += price * quantity;
            }
        });

        // Memperbarui total harga di tampilan
        const totalPriceElement = document.querySelector('.flex.justify-between h3.text-lg.font-bold');
        totalPriceElement.innerText = 'Rp. ' + totalPrice.toLocaleString('id-ID'); // Format ke IDR
    }
    const debouncedCalculateTotalPrice = debounce(calculateTotalPrice, 500);


    // Menangani tombol decrement
    const decrementButtons = document.querySelectorAll('.decrement-button');
    decrementButtons.forEach(button => {
        button.addEventListener('click', function () {
           debouncedCalculateTotalPrice()
        });
    });

    // Menangani tombol increment
    const incrementButtons = document.querySelectorAll('.increment-button');
    incrementButtons.forEach(button => {
        button.addEventListener('click', function () {
            debouncedCalculateTotalPrice(); // Hitung ulang total harga
        });
    });

    // Hitung total harga awal saat halaman dimuat
    debouncedCalculateTotalPrice();
    document.getElementById('doCheckout').addEventListener('click', function () {

        fetch('/checkout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf_token,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.snapToken) {
                    snap.pay(data.snapToken, {
                        onSuccess: function(result) {
                            console.log('Payment Success:', result);
                            alert('Payment Success! Order ID: ' + result.order_id);
                            // Lakukan tindakan setelah pembayaran berhasil
                        },
                        onPending: function(result) {
                            console.log('Payment Pending:', result);
                            alert('Payment Pending! Order ID: ' + result.order_id);
                            // Lakukan tindakan jika pembayaran masih pending
                        },
                        onError: function(result) {
                            console.log('Payment Error:', result);
                            alert('Payment Error! Please try again.');
                            // Lakukan tindakan jika terjadi kesalahan
                        },
                        onClose: function() {
                            console.log('Payment Dialog Closed');
                            alert('Payment dialog closed. Please try again.');
                            // Tindakan jika dialog pembayaran ditutup
                        }
                    }); 
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
    });
});
