<!-- resources/views/keyboard.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Remote Keyboard</title>
    <!-- Add your CSS and JS files here -->
    <style>
        .key {
            width: 50px;
            height: 50px;
            border: 1px solid #ccc;
            margin: 5px;
            cursor: pointer;
        }

        .lit {
            background-color: red;
        }
    </style>

</head>
<body>
    <div>
        <h1>Remote Keyboard</h1>
        <div>
            <div>
                <button id="acquireControlButton">Acquire Control</button>
                <span id="controlStatus">Control:
                    @if ($control)
                        User {{ $control->user_id }}
                    @else
                        None
                    @endif
                </span>
            </div>
            <div>
                <table>
                    @for ($row = 0; $row < 2; $row++)
                        <tr>
                            @for ($col = 0; $col < 5; $col++)
                                @php
                                    $keyId = $row * 5 + $col + 1;
                                    $keyState = $keyboardKeys->firstWhere('id', $keyId)->state ?? false;
                                @endphp
                                <td>
                                    <div class="key {{ $keyState ? 'lit' : '' }}"
                                         data-key-id="{{ $keyId }}"
                                         data-key-state="{{ $keyState ? 1 : 0 }}"></div>
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Function to handle key click
        function handleKeyClick(keyId) {
        const key = $(`.key${keyId}`);
        const isLit = key.hasClass('lit');
        const newState = isLit ? 0 : 1;
        var content = $("#controlStatus").text();
        var userId = content.match(/User (\d+)/)[1];
            $.ajax({
                url: '/keyboard/update-key-state',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    key_id: keyId,
                    state: newState
                },
                success: function (response) {
                if (response.success) {
                    key.toggleClass('lit', newState === 1);

                    // Update the color of the key based on the user ID
                    if (userId === '1') {
                        $('#acquire-control-btn').css('background-color', 'yellow');
                        key.toggleClass('yellow', newState === 1);
                    } else {
                        key.toggleClass('red', newState === 1);
                    }
                }
            },
                error: function (xhr, status, error) {
                    console.error('Error updating key state:', error);
                }
            });
        }
        $(document).on('click', '.key', function () {
            const keyId = $(this).data('key-id');
            handleKeyClick(keyId);
        });
        // Function to acquire control
        function acquireControl(userId) {
            $.ajax({
                url: '/keyboard/acquire-control',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId
                },
                success: function (response) {
                    if (response.success) {
                        $('#controlStatus').text('Control: User ' + userId);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error acquiring control:', error);
                }
            });
        }

        jQuery(document).ready(function ($) {
            const keyId = $(this).data('key-id');
            handleKeyClick(keyId);
        });

        $('#acquireControlButton').click(function () {
            const userId = 1;
            acquireControl(userId);
        });

        // Poll the server at regular intervals for updates
        function pollServer() {
            $.ajax({
                url: '/keyboard/poll',
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        response.keyboardKeys.forEach(key => {
                            const keyElement = $(`[data-key-id="${key.id}"]`);
                            keyElement.toggleClass('lit', key.state === 1);
                        });

                        if (response.control) {
                            $('#controlStatus').text('Control: User ' + response.control.user_id);
                        } else {
                            $('#controlStatus').text('Control: None');
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error polling server:', error);
                },
                complete: function () {
                    setTimeout(pollServer, 1000);
                }
            });
        }

        pollServer();
    </script>

</body>
</html>
