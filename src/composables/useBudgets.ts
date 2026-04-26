import { ref, computed, onUnmounted } from 'vue'
import { liveQuery } from 'dexie'
import { BudgetService, type BudgetWithSpent } from '@/services/BudgetService'
import type { Budget } from '@/types'

export function useBudgets() {
  const budgets   = ref<BudgetWithSpent[]>([])
  const isLoading = ref(true)

  const subscription = liveQuery(() => BudgetService.getAll())
    .subscribe({
      next:  result => { budgets.value = result; isLoading.value = false },
      error: err    => { console.error(err);      isLoading.value = false },
    })

  onUnmounted(() => subscription.unsubscribe())

  const totalAllocated = computed(() =>
    budgets.value.reduce((sum, b) => sum + b.amount, 0)
  )

  const totalSpent = computed(() =>
    budgets.value.reduce((sum, b) => sum + b.spent, 0)
  )

  function getById(id: number): BudgetWithSpent | undefined {
    return budgets.value.find(b => b.id === id)
  }

  async function create(budget: Omit<Budget, 'id'>) {
    return BudgetService.create(budget)
  }

  async function update(id: number, changes: Partial<Omit<Budget, 'id'>>) {
    return BudgetService.update(id, changes)
  }

  async function remove(id: number) {
    return BudgetService.remove(id)
  }

  return { budgets, isLoading, totalAllocated, totalSpent, getById, create, update, remove }
}
