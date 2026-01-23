<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="modelValue"
        class="modal-overlay"
        @click.self="close"
      >
        
        <div class="modal-container">
          <header class="modal-header">
            <!--Definim un botó "X" per a tancar el modal-->
            <button class="close-btn" @click="close">✕</button>
          </header>

          <section class="modal-body">
            <!--El contingut del modal anirà ací-->
            <slot />
          </section>

          <footer class="modal-footer">
            <!--I ací el contingut del peu de pàgina si es referència com a:
            <template #footer>
              "I ací dins el contingut"
            </template>
            -->
            <slot name="footer" />
          </footer>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch, onMounted, onUnmounted } from 'vue'

// Definim el valor del modal per a definir si està obert o no
const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])

// Quan tanquem desaparixera el modal
const close = () => {
  emit('update:modelValue', false)
}

// Bloqueja el scroll
watch(() => props.modelValue, (value) => {
  document.body.style.overflow = value ? 'hidden' : ''
})

</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-container {
  background: white;
  width: 90%;
  max-width: 500px;
  border-radius: 12px;
  overflow: hidden;
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
}

.modal-footer {
  border-top: 1px solid #eee;
}

.modal-body {
  padding: 1rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

</style>
