import { useRegisterSW } from 'virtual:pwa-register/vue'

const sw = useRegisterSW()

export function usePwa() {
    return sw
}