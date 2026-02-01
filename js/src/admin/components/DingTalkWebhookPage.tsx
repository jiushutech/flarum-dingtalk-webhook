import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import WebhookCard from './WebhookCard';
import WebhookSettingsModal from './WebhookSettingsModal';
import type Webhook from '../models/Webhook';
import type Mithril from 'mithril';

export default class DingTalkWebhookPage extends ExtensionPage {
  webhooks: Webhook[] = [];
  loading: boolean = true;

  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);
    this.loadWebhooks();
  }

  async loadWebhooks() {
    this.loading = true;
    m.redraw();

    try {
      const webhooks = await app.store.find<Webhook[]>('dingtalk-webhooks');
      this.webhooks = webhooks as Webhook[];
    } catch (error) {
      console.error('Failed to load webhooks:', error);
      this.webhooks = [];
    }

    this.loading = false;
    m.redraw();
  }

  content() {
    return (
      <div className="DingTalkWebhookPage">
        <div className="DingTalkWebhookPage-header">
          <div className="container">
            <p className="DingTalkWebhookPage-description">
              {app.translator.trans('dingtalk-webhook.admin.settings.help.general')}
            </p>
          </div>
        </div>

        <div className="DingTalkWebhookPage-content">
          <div className="container">
            {this.loading ? (
              <LoadingIndicator />
            ) : (
              <div className="DingTalkWebhookPage-webhooks">
                {this.webhooks.length === 0 ? (
                  <div className="DingTalkWebhookPage-empty">
                    <p>{app.translator.trans('dingtalk-webhook.admin.settings.no_webhooks')}</p>
                  </div>
                ) : (
                  <div className="WebhookCards">
                    {this.webhooks.map((webhook) => (
                      <WebhookCard
                        key={webhook.id()}
                        webhook={webhook}
                        onEdit={() => this.editWebhook(webhook)}
                        onDelete={() => this.deleteWebhook(webhook)}
                        onTest={() => this.testWebhook(webhook)}
                      />
                    ))}
                  </div>
                )}

                <div className="DingTalkWebhookPage-actions">
                  <Button
                    className="Button Button--primary"
                    icon="fas fa-plus"
                    onclick={() => this.addWebhook()}
                  >
                    {app.translator.trans('dingtalk-webhook.admin.settings.add_button')}
                  </Button>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    );
  }

  addWebhook() {
    app.modal.show(WebhookSettingsModal, {
      webhook: null,
      onSave: () => this.loadWebhooks(),
    });
  }

  editWebhook(webhook: Webhook) {
    app.modal.show(WebhookSettingsModal, {
      webhook,
      onSave: () => this.loadWebhooks(),
    });
  }

  async deleteWebhook(webhook: Webhook) {
    if (!confirm(app.translator.trans('dingtalk-webhook.admin.settings.card.delete_confirm') as string)) {
      return;
    }

    try {
      await webhook.delete();
      this.webhooks = this.webhooks.filter((w) => w.id() !== webhook.id());
      m.redraw();
    } catch (error) {
      console.error('Failed to delete webhook:', error);
    }
  }

  async testWebhook(webhook: Webhook) {
    try {
      const response = await app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/dingtalk-webhooks/' + webhook.id() + '/test',
      });

      if (response.success) {
        app.alerts.show({ type: 'success' }, response.message || app.translator.trans('dingtalk-webhook.admin.settings.card.test_success'));
      } else {
        app.alerts.show({ type: 'error' }, response.message || app.translator.trans('dingtalk-webhook.admin.settings.card.test_failed'));
      }
    } catch (error: any) {
      const message = error.response?.message || error.message || app.translator.trans('dingtalk-webhook.admin.settings.card.test_failed');
      app.alerts.show({ type: 'error' }, message);
    }
  }
}
