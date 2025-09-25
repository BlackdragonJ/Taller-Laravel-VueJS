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

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const router = useRouter();
const route = useRoute();
const id = route.params.id as string | undefined;
const isEdit = computed(() => !!id);

const valid = ref(false);
const saving = ref(false);
const errorMsg = ref("");
const okMsg = ref("");

const roles = ["admin", "user"];

const form = ref({
  nombre: "",
  email: "",
  rol: "user",
  password: "", // solo al crear
});

const rules = {
  required: (v: string) => !!v || "Requerido",
  email: (v: string) => /.+@.+\..+/.test(v) || "Email inválido",
  min6: (v: string) =>
    (!v && isEdit.value) || v.length >= 6 || "Mínimo 6 caracteres",
};

onMounted(async () => {
  if (isEdit.value) {
    try {
      const { data } = await api.get(`/usuarios/getUser/${id}`);
      form.value.nombre = data.nombre;
      form.value.email = data.email;
      form.value.rol = data.rol || "user";
    } catch (e: any) {
      errorMsg.value =
        e?.response?.data?.message || "No se pudo cargar el usuario";
    }
  }
});

async function onSubmit() {
  errorMsg.value = "";
  okMsg.value = "";
  if (!valid.value) return;

  try {
    saving.value = true;
    if (isEdit.value) {
      await api.put(`/usuarios/updateUser/${id}`, {
        nombre: form.value.nombre,
        email: form.value.email,
        rol: form.value.rol,
      });
      okMsg.value = "Usuario actualizado";
    } else {
      await api.post("/usuarios/addUser", {
        nombre: form.value.nombre,
        email: form.value.email,
        rol: form.value.rol,
        password: form.value.password,
      });
      okMsg.value = "Usuario creado";
      form.value = { nombre: "", email: "", rol: "user", password: "" };
    }
  } catch (e: any) {
    errorMsg.value = e?.response?.data?.message || "Error al guardar";
  } finally {
    saving.value = false;
  }
}

function goList() {
  router.push("/usuarios");
}
</script>
