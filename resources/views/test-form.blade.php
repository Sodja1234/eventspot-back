@extends('layouts.app')

@section('title', 'Création de compte')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Création de compte</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nom complet -->
                <div class="mb-3">
                    <label for="fullname" class="form-label">Nom complet</label>
                    <input type="text" name="fullname" class="form-control" id="fullname" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>

                <!-- Mot de passe -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>

                 <!-- Mot de passe confirmation -->
                 <div class="mb"password_confirmation" class="form-label">Mot de passe confirmation</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                </div>

                <!-- Rôle -->
                <div class="mb-3">
                    <label for="role" class="form-label">Rôle</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="">-- Sélectionnez un rôle --</option>
                        <option value="organisateur">Organisateur</option>
                        <option value="public">Public</option>
                    </select>
                </div>

                <!-- Nom de l'organisateur (visible seulement si organisateur) -->
                <div class="mb-3" id="nom_organisateur_group" style="display: none;">
                    <label for="nom_organis" class="form-label">Nom de l'organisateur</label>
                    <input type="text" name="nom_organis" class="form-control" id="nom_organis">
                </div>

                <!-- Bouton -->
                <button type="submit" class="btn btn-primary">Créer le compte</button>
            </form>
        </div>
    </div>

    <!-- Script pour afficher/masquer le champ "Nom de l'organisateur" -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const roleSelect = document.getElementById("role");
            const organisateurGroup = document.getElementById("nom_organisateur_group");

            roleSelect.addEventListener("change", function () {
                if (this.value === "organisateur") {
                    organisateurGroup.style.display = "block";
                } else {
                    organisateurGroup.style.display = "none";
                }
            });
        });
    </script>
@endsection
