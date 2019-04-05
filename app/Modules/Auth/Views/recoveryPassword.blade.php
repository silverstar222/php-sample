<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{public_path}}/css/jquery.sweet-modal.css">
    <title>Easy Meals - Восстановление пароля</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-4 mt-5">
                <form class="form-signin">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 font-weight-normal">Новый пароль</h1>
                    </div>

                    <div class="form-label-group my-2">
                        <input type="password" min="8" name="password" id="inputPassword" class="form-control" placeholder="Новый пароль" required autofocus>
                    </div>

                    <div class="form-label-group my-2">
                        <input type="password" min="8" name="confirm_password" id="inputPassword1" class="form-control" placeholder="Подтвердите новый пароль" required>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block btn-submit" type="submit">Установить</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{public_path}}/js/jquery3-2-1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.9.0/sha256.min.js"></script>
    <script src="{{public_path}}/js/jquery.sweet-modal.js"></script>
    <script>
        var token = '{{$token}}';

        function sweet_modal(text,type,time) {
            $.sweetModal({
                content: text,
                icon: type,
                timeout:time
            });
        }

        $('form.form-signin').on('submit', function(e){
            e.preventDefault();
            var password = $(this).find('input[name="password"]').val();
            var confirmNewPassword = $(this).find('input[name="confirm_password"]').val();

            if (password != confirmNewPassword) {
                sweet_modal('Пароли не совпадают!', 'error', 3000);
                return false;
            }

            if (password.length <8) {
                sweet_modal('Минимальная длина пароля  - 8 символов!', 'error', 3000);
                return false;
            }

            var formData = new FormData();
            formData.append('new_password',sha256(password));
            formData.append('token',token);

            $.ajax({
                url: "/api/v1.0/auth/password/recovery/set",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    '_token' : '{{ csrf_token() }}'
                },
                dataType: 'JSON',
                beforeSend: function() {
                    $('.lds-roller').css('display', 'block');
                    $('.btn-submit').attr('disabled', 'disabled');
                    $('form.form-signin').css('pointer-events', 'none');
                },
                success: function (data) {
                    if (data.success == true) {
                        sweet_modal('Success!', 'success', 1000);
                        setTimeout(function () {
                            window.location = '{{asset('/recovery/success')}}';
                        }, 1000);
                    } else {
                        sweet_modal(data.message, 'error', 3000);
                    }
                    $('.btn-submit').removeAttr('disabled');
                },error: function (data) {
                    console.log(data);
                    $('.lds-roller').css('display', 'none');
                    $('.btn-submit').removeAttr('disabled');
                    $('form.form-signin').css('pointer-events', 'all');
                    if (data.responseJSON.message) {
                        sweet_modal(data.responseJSON.message, 'error', 3000);
                    } else {
                        sweet_modal('Something went wrong', 'error', 3000);
                    }
                },done: function () {
                    $('.lds-roller').css('display', 'none');
                    $('.btn-submit').removeAttr('disabled');
                    $('.lds-roller').css('display', 'none');
                }
            })
        });
    </script>
</body>
</html>