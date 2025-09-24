<template>
  <v-container class="py-6" style="max-width: 720px">
    <v-card class="pa-6">
      <div class="d-flex justify-space-between align-center mb-4">
        <h2 class="text-h6">
          {{ isEdit ? "Editar usuario" : "Nuevo usuario" }}
        </h2>
        <v-btn variant="text" @click="goList">Volver</v-btn>
      </div>

      <v-form v-model="valid" @submit.prevent="onSubmit">
        <v-row dense>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.nombre"
              label="Nombre"
              :rules="[rules.required]"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.email"
              label="Email"
              type="email"
              :rules="[rules.required, rules.email]"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              v-model="form.rol"
              :items="roles"
              label="Rol"
              :rules="[rules.required]"
            />
          </v-col>
          <v-col cols="12" md="6" v-if="!isEdit">
            <v-text-field
              v-model="form.password"
              label="Password"
              type="password"
              :rules="[rules.required, rules.min6]"
            />
          </v-col>
        </v-row>

        <v-alert
          v-if="errorMsg"
          type="error"
          variant="tonal"
          class="mb-3"
          :text="errorMsg"
        />
        <v-alert
          v-if="okMsg"
          type="success"
          variant="tonal"
          class="mb-3"
          :text="okMsg"
        />

        <div class="d-flex gap-2">
          <v-btn color="primary" type="submit" :loading="saving">
            {{ isEdit ? "Guardar cambios" : "Crear usuario" }}
          </v-btn>
          <v-btn variant="tonal" @click="goList">Cancelar</v-btn>
        </div>
      </v-form>
    </v-card>
  </v-container>
</template>
