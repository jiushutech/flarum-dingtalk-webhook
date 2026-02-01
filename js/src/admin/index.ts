import app from 'flarum/admin/app';
import Webhook from './models/Webhook';
import DingTalkWebhookPage from './components/DingTalkWebhookPage';

app.initializers.add('jiushutech-dingtalk-webhook', () => {
  app.store.models['dingtalk-webhooks'] = Webhook;

  app.extensionData
    .for('jiushutech-dingtalk-webhook')
    .registerPage(DingTalkWebhookPage);
});
