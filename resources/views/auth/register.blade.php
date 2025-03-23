<x-layouts.main-layout pageTitle="Registro Nuevo Usuario">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card p-5">
                    <p class="display-6 text-center">Nueva Cuenta</p>

                    <form action="#" method="post">

                        <div class="mb-3">
                            <label for="username" class="form-label">Usuário</label>
                            <input type="text" class="form-control" id="username" name="username">
                            <div class="text-danger">[mensagem de erro]</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <div class="text-danger">[mensagem de erro]</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="text-danger">[mensagem de erro]</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation " class="form-label">Confirmar senha</label>
                            <input type="password" class="form-control" id="password_confirmation " name="password_confirmation ">
                            <div class="text-danger">[mensagem de erro]</div>
                        </div>

                        <div class="row mt-4">
                            <div class="col">
                                <div class="mb-3">
                                    <a href="#">Já tenho conta de usuário</a>
                                </div>
                                <div>
                                    <a href="#">Esqueci a minha senha</a>
                                </div>
                            </div>
                            <div class="col text-end align-self-center">
                                <button type="submit" class="btn btn-secondary px-5">CRIAR CONTA</button>
                            </div>
                        </div>

                    </form>

                    <div class="alert alert-danger text-center mt-3">
                        [mensagem de erro]
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.main-layout>