<script setup>
import { IonIcon } from '@ionic/vue';
import {logoGithub, globeOutline, desktopOutline} from 'ionicons/icons';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <footer class="tns-footer">
    <div class="tns-footer-copy">© 2026</div>
    <span class="tns-footer-sep">·</span>
    <a
        href="https://axel-reviron.fr/en"
        target="_blank"
        rel="noopener noreferrer"
        class="tns-footer-link"
    >
      <ion-icon :icon="desktopOutline" />
      <span>Axel Reviron</span>
    </a>
    <span class="tns-footer-sep">·</span>
    <a
      href="https://github.com/AxelReviron/Tunasse"
      target="_blank"
      rel="noopener noreferrer"
      class="tns-footer-link"
    >
      <ion-icon :icon="logoGithub" />
      <span>{{ t('footer.sourceCode') }}</span>
    </a>
  </footer>
</template>

<style scoped>
.tns-footer {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 8px 10px;
  padding: 20px 16px 32px;
  font-family: var(--tns-font);
}

.tns-footer-copy {
  font-size: 12px;
  font-weight: 500;
  color: var(--tns-fg3);
}

.tns-footer-link {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 500;
  color: var(--tns-fg3);
  text-decoration: none;
  transition: color 0.15s;
}
.tns-footer-link:hover { color: var(--tns-fg2); }
.tns-footer-link ion-icon { font-size: 14px; }

.tns-footer-sep {
  font-size: 12px;
  color: var(--tns-fg3);
}
</style>
