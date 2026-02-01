import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import icon from 'flarum/common/helpers/icon';
import type Webhook from '../models/Webhook';
import type Mithril from 'mithril';

interface WebhookCardAttrs {
  webhook: Webhook;
  onEdit: () => void;
  onDelete: () => void;
  onTest: () => void;
}

export default class WebhookCard extends Component<WebhookCardAttrs> {
  view() {
    const { webhook, onEdit, onDelete, onTest } = this.attrs;
    const events = webhook.events() || [];
    const eventCount = events.length;
    const hasError = !!webhook.error();
    const isValid = webhook.isValid();

    return (
      <div className={`WebhookCard ${hasError ? 'WebhookCard--error' : ''} ${!isValid ? 'WebhookCard--invalid' : ''}`}>
        <div className="WebhookCard-header">
          <div className="WebhookCard-title">
            {icon('fas fa-bell')}
            <span className="WebhookCard-name">{webhook.name() || app.translator.trans('dingtalk-webhook.admin.nav.title')}</span>
          </div>
          <div className="WebhookCard-actions">
            <Button
              className="Button Button--icon"
              icon="fas fa-paper-plane"
              onclick={onTest}
              title={app.translator.trans('dingtalk-webhook.admin.settings.card.test_button')}
            />
            <Button
              className="Button Button--icon"
              icon="fas fa-edit"
              onclick={onEdit}
              title={app.translator.trans('dingtalk-webhook.admin.settings.card.edit_button')}
            />
            <Button
              className="Button Button--icon Button--danger"
              icon="fas fa-trash"
              onclick={onDelete}
              title={app.translator.trans('dingtalk-webhook.admin.settings.card.delete_button')}
            />
          </div>
        </div>

        <div className="WebhookCard-body">
          <div className="WebhookCard-info">
            <div className="WebhookCard-url">
              {icon('fas fa-link')}
              <span className="WebhookCard-url-text" title={webhook.url()}>
                {this.truncateUrl(webhook.url())}
              </span>
            </div>

            {webhook.hasSecret() && (
              <div className="WebhookCard-secret">
                {icon('fas fa-key')}
                <span>{app.translator.trans('dingtalk-webhook.admin.settings.modal.secret_label')}: ******</span>
              </div>
            )}
          </div>

          <div className="WebhookCard-events">
            {eventCount > 0 ? (
              <div className="WebhookCard-events-badges">
                {this.renderEventBadges(events)}
              </div>
            ) : (
              <div className="WebhookCard-events-empty">
                {icon('fas fa-exclamation-triangle')}
                <span>{app.translator.trans('dingtalk-webhook.admin.settings.card.no_events')}</span>
              </div>
            )}
          </div>

          {hasError && (
            <div className="WebhookCard-error">
              {icon('fas fa-exclamation-circle')}
              <span>{webhook.error()}</span>
            </div>
          )}
        </div>

        <div className="WebhookCard-footer">
          <div className="WebhookCard-meta">
            <span className="WebhookCard-event-count">
              {app.translator.trans('dingtalk-webhook.admin.settings.card.events_enabled', { count: eventCount })}
            </span>
            {webhook.maxPostContentLength() > 0 && (
              <span className="WebhookCard-content-length">
                {icon('fas fa-text-width')}
                {webhook.maxPostContentLength()} 字
              </span>
            )}
          </div>
        </div>
      </div>
    );
  }

  truncateUrl(url: string): string {
    if (!url) return '';
    if (url.length <= 60) return url;
    return url.substring(0, 50) + '...' + url.substring(url.length - 10);
  }

  renderEventBadges(events: string[]): Mithril.Children {
    const eventLabels = this.getEventLabels();
    const maxDisplay = 4;
    const displayEvents = events.slice(0, maxDisplay);
    const remaining = events.length - maxDisplay;

    return (
      <>
        {displayEvents.map((event) => (
          <span className="WebhookCard-event-badge" key={event}>
            {eventLabels[event] || event}
          </span>
        ))}
        {remaining > 0 && (
          <span className="WebhookCard-event-badge WebhookCard-event-badge--more">
            +{remaining}
          </span>
        )}
      </>
    );
  }

  getEventLabels(): Record<string, string> {
    return {
      'Flarum\\Discussion\\Event\\Started': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.started') as string,
      'Flarum\\Discussion\\Event\\Renamed': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.renamed') as string,
      'Flarum\\Discussion\\Event\\Hidden': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.hidden') as string,
      'Flarum\\Discussion\\Event\\Restored': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.restored') as string,
      'Flarum\\Discussion\\Event\\Deleted': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.deleted') as string,
      'Flarum\\Post\\Event\\Posted': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.posted') as string,
      'Flarum\\Post\\Event\\Revised': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.revised') as string,
      'Flarum\\Post\\Event\\Hidden': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.hidden') as string,
      'Flarum\\Post\\Event\\Restored': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.restored') as string,
      'Flarum\\Post\\Event\\Deleted': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.deleted') as string,
      'Flarum\\User\\Event\\Registered': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.registered') as string,
      'Flarum\\User\\Event\\Renamed': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.renamed') as string,
      'Flarum\\User\\Event\\Deleted': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.deleted') as string,
      'Flarum\\Group\\Event\\Created': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.created') as string,
      'Flarum\\Group\\Event\\Renamed': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.renamed') as string,
      'Flarum\\Group\\Event\\Deleted': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.deleted') as string,
      'Flarum\\Approval\\Event\\PostWasApproved': app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.approval.postwasapproved') as string,
    };
  }
}
