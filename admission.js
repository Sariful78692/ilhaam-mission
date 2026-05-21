document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('admissionForm');
    const notification = document.getElementById('notification');
    const formSection = document.getElementById('formSection');
    const confirmationSection = document.getElementById('confirmationSection');
    const finalRollNumber = document.getElementById('finalRollNumber');
    const detailsList = document.getElementById('detailsList');
    const submitButton = document.getElementById('submitButton');
    const rollNumberInput = document.getElementById('roll_number');

    // Show notification
    function showNotification(message, type) {
        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 5000);
    }

    // Populate confirmation details
    function populateDetails(data) {
        detailsList.innerHTML = '';
        for (const [key, value] of Object.entries(data)) {
            if (value && value !== 'NIL' && key !== 'csrf_token' && key !== 'payment_id' && key !== 'order_id') {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}:</strong> ${value}`;
                detailsList.appendChild(li);
            }
        }
    }

    // Print confirmation
    window.printConfirmation = function () {
        window.print();
    };

    // Fetch roll number
    async function fetchRollNumber() {
        try {
            const response = await fetch('generate_roll.php');
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const text = await response.text();
            console.log('Roll number response:', text);
            const data = JSON.parse(text);
            if (data.roll_number) {
                rollNumberInput.value = data.roll_number;
            } else {
                showNotification('Failed to generate roll number', 'error');
            }
        } catch (error) {
            showNotification('Error fetching roll number', 'error');
            console.error('Roll number fetch error:', error);
        }
    }

    // Initiate Razorpay payment
    async function initiatePayment(formData) {
        try {
            const response = await fetch('api.php?action=create-order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ amount: 10000 }) // ₹100 in paise
            });
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const text = await response.text();
            console.log('Create order response:', text);
            const orderData = JSON.parse(text);
            if (!orderData.order_id) {
                throw new Error('Failed to create order');
            }

            const options = {
                key: 'rzp_test_RGlwIrCZ1EddKl',
                amount: 10000,
                currency: 'INR',
                name: 'Ilhaam Mission',
                description: 'Admission Application Fee',
                order_id: orderData.order_id,
                handler: async function (response) {
                    try {
                        const verifyResponse = await fetch('api.php?action=verify-payment', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            })
                        });
                        if (!verifyResponse.ok) {
                            throw new Error(`HTTP error! Status: ${verifyResponse.status}`);
                        }
                        const verifyText = await verifyResponse.text();
                        console.log('Verify payment response:', verifyText);
                        const verifyData = JSON.parse(verifyText);
                        if (verifyData.status === 'success') {
                            // Submit form with payment details
                            formData.payment_id = response.razorpay_payment_id;
                            formData.order_id = response.razorpay_order_id;
                            formData.status = 'pending';
                            submitForm(formData);
                        } else {
                            showNotification('Payment verification failed: ' + (verifyData.message || 'Unknown error'), 'error');
                            submitButton.disabled = false;
                        }
                    } catch (error) {
                        showNotification('Error verifying payment: ' + error.message, 'error');
                        submitButton.disabled = false;
                        console.error('Payment verification error:', error);
                    }
                },
                prefill: {
                    name: formData.student_name || '',
                    contact: formData.contact_number || ''
                },
                theme: {
                    color: '#4361ee'
                }
            };
            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function (response) {
                showNotification('Payment failed: ' + (response.error.description || 'Unknown error'), 'error');
                submitButton.disabled = false;
                console.error('Payment failed:', response.error);
            });
            rzp.open();
        } catch (error) {
            showNotification('Error initiating payment: ' + error.message, 'error');
            submitButton.disabled = false;
            console.error('Payment initiation error:', error);
        }
    }

    // Submit form data
    async function submitForm(formData) {
        try {
            console.log('Submitting form data:', JSON.stringify(formData));
            const response = await fetch('admission.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const text = await response.text();
            console.log('Form submission response (raw):', text);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON response: ' + text);
            }

            if (result.status === 'success') {
                formSection.style.display = 'none';
                confirmationSection.classList.add('active');
                finalRollNumber.textContent = result.roll_number;
                populateDetails(formData);
                showNotification(result.message, 'success');
            } else {
                showNotification(result.message || 'Submission failed', 'error');
                submitButton.disabled = false;
            }
        } catch (error) {
            showNotification('Form submission error: ' + error.message, 'error');
            submitButton.disabled = false;
            console.error('Form submission error:', error);
        }
    }

    // Form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitButton.disabled = true;

        const formData = {
            csrf_token: form.querySelector('[name="csrf_token"]').value,
            roll_number: form.querySelector('[name="roll_number"]').value,
            applied_class: form.querySelector('[name="applied_class"]').value,
            academic_session: form.querySelector('[name="academic_session"]').value,
            applied_for: form.querySelector('[name="applied_for"]:checked')?.value || '',
            student_name: form.querySelector('[name="student_name"]').value,
            dob: form.querySelector('[name="dob"]').value,
            gender: form.querySelector('[name="gender"]:checked')?.value || '',
            religion: form.querySelector('[name="religion"]').value,
            category: form.querySelector('[name="category"]:checked')?.value || '',
            aadhar: form.querySelector('[name="aadhar"]').value,
            village: form.querySelector('[name="village"]').value,
            post_office: form.querySelector('[name="post_office"]').value,
            police_station: form.querySelector('[name="police_station"]').value,
            district: form.querySelector('[name="district"]').value,
            pin_code: form.querySelector('[name="pin_code"]').value,
            state: form.querySelector('[name="state"]').value,
            contact_number: form.querySelector('[name="contact_number"]').value,
            whatsapp_number: form.querySelector('[name="whatsapp_number"]').value,
            father_name: form.querySelector('[name="father_name"]').value,
            father_qualification: form.querySelector('[name="father_qualification"]').value || 'NIL',
            father_occupation: form.querySelector('[name="father_occupation"]').value || 'NIL',
            father_monthly_income: form.querySelector('[name="father_monthly_income"]').value || 'NIL',
            mother_name: form.querySelector('[name="mother_name"]').value,
            mother_qualification: form.querySelector('[name="mother_qualification"]').value || 'NIL',
            mother_occupation: form.querySelector('[name="mother_occupation"]').value || 'NIL',
            mother_monthly_income: form.querySelector('[name="mother_monthly_income"]').value || 'NIL',
            local_guardian_name: form.querySelector('[name="local_guardian_name"]').value || 'NIL',
            local_guardian_address: form.querySelector('[name="local_guardian_address"]').value || 'NIL',
            relation_with_guardian: form.querySelector('[name="relation_with_guardian"]').value || 'NIL',
            guardian_contact_number: form.querySelector('[name="guardian_contact_number"]').value || 'NIL',
            previous_institution: form.querySelector('[name="previous_institution"]').value,
            declaration: form.querySelector('[name="declaration"]').checked ? 'agree' : ''
        };

        // Client-side validation
        const requiredFields = [
            'roll_number', 'applied_class', 'academic_session', 'applied_for', 'student_name', 'dob', 'gender',
            'religion', 'category', 'aadhar', 'village', 'post_office', 'police_station', 'district', 'pin_code',
            'state', 'contact_number', 'whatsapp_number', 'father_name', 'mother_name', 'previous_institution',
            'declaration'
        ];
        for (const field of requiredFields) {
            if (!formData[field] || formData[field] === 'NIL') {
                showNotification(`Please fill in ${field.replace(/_/g, ' ')}`, 'error');
                submitButton.disabled = false;
                return;
            }
        }

        initiatePayment(formData);
    });

    // Back to top button
    const backToTop = document.querySelector('.back-to-top');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    backToTop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Fetch roll number on page load
    fetchRollNumber();
});