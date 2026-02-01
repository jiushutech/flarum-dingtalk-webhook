import app from 'flarum/admin/app';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Switch from 'flarum/common/components/Switch';
import Stream from 'flarum/common/utils/Stream';
import type Webhook from '../models/Webhook';
import type Mithril from 'mithril';

interface WebhookSettingsModalAttrs {
  webhook: Webhook | null;
  onSave: () => void;
}

interface EventGroup {
  title: string;
  events: { key: string; label: string }[];
}

export default class WebhookSettingsModal extends Modal<WebhookSettingsModalAttrs> {
  name!: Stream<string>;
  url!: Stream<string>;
  secret!: Stream<string>;
  events!: Stream<string[]>;
  groupId!: Stream<number>;
  extraText!: Stream<string>;
  maxPostContentLength!: Stream<number>;
  usePlainText!: Stream<boolean>;
  includeTags!: Stream<boolean>;
  messageTemplate!: Stream<string>;
  activeTab!: Stream<string>;
  saving: boolean = false;

  oninit(vnode: Mithril.Vnode<WebhookSettingsModalAttrs>) {
    super.oninit(vnode);

    const webhook = this.attrs.webhook;

    this.name = Stream(webhook?.name() || '');
    this.url = Stream(webhook?.url() || '');
    this.secret = Stream('');
    this.events = Stream(webhook?.events() || []);
    this.groupId = Stream(webhook?.groupId() || 2);
    this.extraText = Stream(webhook?.extraText() || '');
    this.maxPostContentLength = Stream(webhook?.maxPostContentLength() || 200);
    this.usePlainText = Stream(webhook?.usePlainText() || false);
    this.includeTags = Stream(webhook?.includeTags() || false);
    this.messageTemplate = Stream(webhook?.messageTemplate() || '');
    this.activeTab = Stream('basic');
  }

  className() {
    return 'WebhookSettingsModal Modal--large';
  }

  title() {
    return app.translator.trans('dingtalk-webhook.admin.settings.modal.title');
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="WebhookSettingsModal-tabs">
          <button
            className={`WebhookSettingsModal-tab ${this.activeTab() === 'basic' ? 'active' : ''}`}
            onclick={() => this.activeTab('basic')}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.tabs.basic')}
          </button>
          <button
            className={`WebhookSettingsModal-tab ${this.activeTab() === 'events' ? 'active' : ''}`}
            onclick={() => this.activeTab('events')}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.tabs.events')}
          </button>
          <button
            className={`WebhookSettingsModal-tab ${this.activeTab() === 'advanced' ? 'active' : ''}`}
            onclick={() => this.activeTab('advanced')}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.tabs.advanced')}
          </button>
        </div>

        <div className="WebhookSettingsModal-content">
          {this.activeTab() === 'basic' && this.renderBasicTab()}
          {this.activeTab() === 'events' && this.renderEventsTab()}
          {this.activeTab() === 'advanced' && this.renderAdvancedTab()}
        </div>

        <div className="WebhookSettingsModal-actions">
          <Button
            className="Button Button--primary"
            loading={this.saving}
            onclick={() => this.save()}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.save_button')}
          </Button>
          <Button
            className="Button"
            onclick={() => this.hide()}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.cancel_button')}
          </Button>
        </div>
      </div>
    );
  }

  renderBasicTab() {
    return (
      <div className="WebhookSettingsModal-tab-content">
        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.name_label')}</label>
          <input
            className="FormControl"
            type="text"
            bidi={this.name}
            placeholder={app.translator.trans('dingtalk-webhook.admin.settings.modal.name_label')}
          />
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.name_help')}</p>
        </div>

        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.url_label')}</label>
          <input
            className="FormControl"
            type="text"
            bidi={this.url}
            placeholder={app.translator.trans('dingtalk-webhook.admin.settings.modal.url_placeholder')}
          />
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.url_help')}</p>
        </div>

        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.secret_label')}</label>
          <input
            className="FormControl"
            type="password"
            bidi={this.secret}
            placeholder={app.translator.trans('dingtalk-webhook.admin.settings.modal.secret_placeholder')}
          />
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.secret_help')}</p>
        </div>

        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.group_label')}</label>
          <select className="FormControl" value={this.groupId()} onchange={(e: Event) => this.groupId(parseInt((e.target as HTMLSelectElement).value))}>
            {this.getGroups().map((group) => (
              <option value={group.id} key={group.id}>{group.name}</option>
            ))}
          </select>
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.group_help')}</p>
        </div>
      </div>
    );
  }

  renderEventsTab() {
    const eventGroups = this.getEventGroups();

    return (
      <div className="WebhookSettingsModal-tab-content WebhookSettingsModal-events">
        {eventGroups.map((group) => (
          <div className="WebhookSettingsModal-event-group" key={group.title}>
            <h4>{group.title}</h4>
            <div className="WebhookSettingsModal-event-list">
              {group.events.map((event) => (
                <Switch
                  key={event.key}
                  state={this.events().includes(event.key)}
                  onchange={(checked: boolean) => this.toggleEvent(event.key, checked)}
                >
                  {event.label}
                </Switch>
              ))}
            </div>
          </div>
        ))}
      </div>
    );
  }

  renderAdvancedTab() {
    return (
      <div className="WebhookSettingsModal-tab-content">
        <div className="Form-group">
          <Switch
            state={this.usePlainText()}
            onchange={(checked: boolean) => this.usePlainText(checked)}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.use_plain_text_label')}
          </Switch>
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.use_plain_text_help')}</p>
        </div>

        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.max_post_content_length_label')}</label>
          <input
            className="FormControl"
            type="number"
            min="0"
            bidi={this.maxPostContentLength}
          />
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.max_post_content_length_help')}</p>
        </div>

        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.extra_text_label')}</label>
          <textarea
            className="FormControl"
            rows={3}
            bidi={this.extraText}
          />
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.extra_text_help')}</p>
        </div>

        <div className="Form-group">
          <Switch
            state={this.includeTags()}
            onchange={(checked: boolean) => this.includeTags(checked)}
          >
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.include_matching_tags_label')}
          </Switch>
          <p className="helpText">{app.translator.trans('dingtalk-webhook.admin.settings.modal.include_matching_tags_help')}</p>
        </div>

        <div className="Form-group">
          <label>{app.translator.trans('dingtalk-webhook.admin.settings.modal.message_template_label')}</label>
          <textarea
            className="FormControl"
            rows={6}
            bidi={this.messageTemplate}
            placeholder={app.translator.trans('dingtalk-webhook.admin.settings.modal.message_template_placeholder') as string}
          />
          <div className="helpText helpText--multiline" style={{ whiteSpace: 'pre-wrap' }}>
            {app.translator.trans('dingtalk-webhook.admin.settings.modal.message_template_help')}
          </div>
        </div>
      </div>
    );
  }

  toggleEvent(eventKey: string, checked: boolean) {
    const events = [...this.events()];
    if (checked) {
      if (!events.includes(eventKey)) {
        events.push(eventKey);
      }
    } else {
      const index = events.indexOf(eventKey);
      if (index > -1) {
        events.splice(index, 1);
      }
    }
    this.events(events);
  }

  getGroups(): { id: number; name: string }[] {
    const groups = app.store.all('groups') as any[];
    return groups.map((group) => ({
      id: group.id(),
      name: group.namePlural(),
    }));
  }

  getEventGroups(): EventGroup[] {
    return [
      {
        title: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.title') as string,
        events: [
          { key: 'Flarum\\Discussion\\Event\\Started', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.started') as string },
          { key: 'Flarum\\Discussion\\Event\\Renamed', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.renamed') as string },
          { key: 'Flarum\\Discussion\\Event\\Hidden', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.hidden') as string },
          { key: 'Flarum\\Discussion\\Event\\Restored', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.restored') as string },
          { key: 'Flarum\\Discussion\\Event\\Deleted', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.discussion.deleted') as string },
        ],
      },
      {
        title: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.title') as string,
        events: [
          { key: 'Flarum\\Post\\Event\\Posted', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.posted') as string },
          { key: 'Flarum\\Post\\Event\\Revised', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.revised') as string },
          { key: 'Flarum\\Post\\Event\\Hidden', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.hidden') as string },
          { key: 'Flarum\\Post\\Event\\Restored', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.restored') as string },
          { key: 'Flarum\\Post\\Event\\Deleted', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.post.deleted') as string },
        ],
      },
      {
        title: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.title') as string,
        events: [
          { key: 'Flarum\\User\\Event\\Registered', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.registered') as string },
          { key: 'Flarum\\User\\Event\\Renamed', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.renamed') as string },
          { key: 'Flarum\\User\\Event\\Deleted', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.user.deleted') as string },
        ],
      },
      {
        title: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.title') as string,
        events: [
          { key: 'Flarum\\Group\\Event\\Created', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.created') as string },
          { key: 'Flarum\\Group\\Event\\Renamed', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.renamed') as string },
          { key: 'Flarum\\Group\\Event\\Deleted', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.group.deleted') as string },
        ],
      },
      {
        title: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.approval.title') as string,
        events: [
          { key: 'Flarum\\Approval\\Event\\PostWasApproved', label: app.translator.trans('dingtalk-webhook.admin.settings.actions.flarum.approval.postwasapproved') as string },
        ],
      },
    ];
  }

  async save() {
    this.saving = true;
    m.redraw();

    try {
      const data: Record<string, any> = {
        name: this.name(),
        url: this.url(),
        events: this.events(),
        groupId: this.groupId(),
        extraText: this.extraText(),
        maxPostContentLength: this.maxPostContentLength(),
        usePlainText: this.usePlainText(),
        includeTags: this.includeTags(),
        messageTemplate: this.messageTemplate(),
      };

      // 只有当用户输入了新密钥时才更新
      if (this.secret()) {
        data.secret = this.secret();
      }

      if (this.attrs.webhook) {
        await this.attrs.webhook.save(data);
      } else {
        await app.store.createRecord('dingtalk-webhooks').save(data);
      }

      this.attrs.onSave();
      this.hide();
    } catch (error) {
      console.error('Failed to save webhook:', error);
    }

    this.saving = false;
    m.redraw();
  }
}
