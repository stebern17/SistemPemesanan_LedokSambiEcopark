import "./bootstrap";
import "flowbite";

let cartData = null; // Definisikan cartData di lingkup global

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

            if (quantity > 0) {
                // Menghitung total harga untuk item ini
                totalPrice += price * quantity;
            } else {
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
                });
            }
        });

        // Memperbarui total harga di tampilan
        const totalPriceElement = document.querySelector('.flex.justify-between h3.text-lg.font-bold');
        totalPriceElement.innerText = 'Rp. ' + totalPrice.toLocaleString('id-ID'); // Format ke IDR
    }

    const debouncedCalculateTotalPrice = debounce(calculateTotalPrice, 500);

    // Fungsi untuk memperbarui jumlah item di keranjang
    function updateCartQuantity(name, quantity, price) {
        fetch('/update-cart-quantity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf_token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                quantity: quantity,
                price: price
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Perbarui total harga di tampilan
                debouncedCalculateTotalPrice();
            } else {
                alert('Error updating cart quantity: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating cart quantity: ' + error.message);
        });
    }

    // Menangani tombol decrement
    const decrementButtons = document.querySelectorAll('.decrement-button');
    decrementButtons.forEach(button => {
        button.addEventListener('click', function () {
            const item = this.closest('.grid.grid-cols-2');
            const quantityInput = item.querySelector('input[data-input-counter]');
            let quantity = parseInt(quantityInput.value);

            if (quantity > 1) {
                quantityInput.value = quantity; // Update input value
                const priceElement = item.querySelector('p');
                const price = parseFloat(priceElement.innerText.replace('Rp. ', '').replace(/\./g, '').trim());
                updateCartQuantity(item.dataset.name, quantity, price); // Update cart quantity on server
            } else {
                // If quantity is 1, remove the item
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
                }).then(() => {
                    item.remove(); // Remove item from DOM
                    debouncedCalculateTotalPrice(); // Recalculate total price
                });
            }
        });
    });

    // Menangani tombol increment
    const incrementButtons = document.querySelectorAll('.increment-button');
    incrementButtons.forEach(button => {
        button.addEventListener('click', function () {
            const item = this.closest('.grid.grid-cols-2');
            const quantityInput = item.querySelector('input[data-input-counter]');
            let quantity = parseInt(quantityInput.value);
            quantityInput.value = quantity; // Update input value
            const priceElement = item.querySelector('p');
            const price = parseFloat(priceElement.innerText.replace('Rp. ', '').replace(/\./g, '').trim());
            updateCartQuantity(item.dataset.name, quantity, price); // Update cart quantity on server
        });
    });

    // Hitung total harga awal saat halaman dimuat
    debouncedCalculateTotalPrice();

    // Fungsi untuk memilih meja
    function selectTable(tableNumber, tableId) {
        // Simpan nomor meja dan ID meja ke dalam objek cartData
        cartData = {
            tableNumber: tableNumber,
            tableId: tableId
        };
    
        // Update elemen p dengan nomor meja yang dipilih
        document.getElementById('selectedTable').innerText = 'Meja Terpilih: ' + tableNumber;
    
        // Kirim data meja ke server
        sendTableDataToServer(tableNumber, tableId);
    
        // Simpan nomor meja ke localStorage
        localStorage.setItem('selectedTable', tableNumber);
        localStorage.setItem('selectedTableId', tableId); // Simpan ID meja ke localStorage
    }
    
    function sendTableDataToServer(tableNumber, tableId) {
        fetch('/save-table', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf_token // Pastikan untuk menyertakan token CSRF untuk Laravel
            },
            body: JSON.stringify({ 
                tableNumber: tableNumber,
                tableId: tableId // Sertakan ID meja dalam body permintaan
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
    
    
    // Menambahkan event listener untuk pemilihan meja
    const tableLinks = document.querySelectorAll('#dropdown a'); // Ambil semua link meja
tableLinks.forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Mencegah link default
        const tableNumber = this.innerText; // Ambil nomor meja dari teks link
        const tableId = this.dataset.tableId; // Ambil ID meja dari data attribute
        selectTable(tableNumber, tableId); // Panggil fungsi selectTable dengan nomor dan ID meja
    });
});

    
    // Cek jika ada meja yang sudah dipilih saat halaman dimuat
    const savedTable = localStorage.getItem('selectedTable', 'selectedTableId');
    if (savedTable) {
        selectTable(savedTable); // Panggil fungsi selectTable dengan nomor meja yang disimpan
    }
    

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
                    },
                    onPending: function(result) {
                        console.log('Payment Pending:', result);
                        alert('Payment Pending! Order ID: ' + result.order_id);
                    },
                    onError: function(result) {
                        console.log('Payment Error:', result);
                        alert('Payment Error! Please try again.');
                    },
                    onClose: function() {
                        console.log('Payment Dialog Closed');
                        alert('Payment dialog closed. Please try again.');
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
