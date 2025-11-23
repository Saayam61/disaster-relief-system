import 'package:app/main_layout.dart';
import 'package:app/models/contribution.dart';
import 'package:app/providers/contribution_provider.dart';
import 'package:app/providers/volapply_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class OrgContribution extends StatefulWidget {
  final int userId;
  const OrgContribution({super.key, required this.userId});

  @override
  State<OrgContribution> createState() => _OrgContributionState();
}

class _OrgContributionState extends State<OrgContribution> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = Provider.of<ContributionProvider>(context, listen: false);
      await provider.getOrgContributions(userId: widget.userId);
      final volProvider = Provider.of<VolapplyProvider>(context, listen: false);
    });
  }
  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<ContributionProvider>(context);
    final contributions = provider.contributions;
    return MainLayout(
      child: SafeArea(
        child: SingleChildScrollView(
          padding: EdgeInsets.all(8),
          child: Column(
            children: [
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.deepPurple,
                  fixedSize: Size(double.maxFinite, 25),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                onPressed: () async {
                  try{
                    await Provider.of<VolapplyProvider>(context, listen: false).applyOrg(
                      userId: widget.userId,
                    );
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('Applied successfully!')),
                    );
                  } catch (e) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('Failed to apply')),
                    );
                  }
                },
                child: Text('Apply as Volunteer',
                  style: TextStyle(color: Colors.white),
                ),
              ),
              Card(
                color: Colors.blueGrey,
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.wallet_giftcard, color: Colors.white),
                      SizedBox(width: 10),
                      Text(
                        'Contributions',
                        style: TextStyle(color: Colors.white),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 16),
              contributions.isEmpty
                  ? SizedBox(
                    width: double.infinity,
                    child: Card(
                        margin: EdgeInsets.symmetric(vertical: 10),
                        elevation: 4,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                        color: Colors.amber.shade50,
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Text(
                            "No Contributions have been registered yet.",
                            style: TextStyle(
                              fontStyle: FontStyle.italic,
                              color: Colors.black,
                            ),
                          ),
                        ),
                      ),
                  )
                  : Column(
                      children: contributions.map((contribution) {
                        return SizedBox(
                          width: double.infinity,
                          child: Card(
                            margin: EdgeInsets.symmetric(vertical: 10),
                            elevation: 4,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            color: Colors.amber.shade50,
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    contribution.name,
                                    style: TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.brown.shade800,
                                    ),
                                  ),
                                  SizedBox(height: 10),
                                  buildRichText("📅 Date: ", getDate(contribution.createdAt)),
                                  buildRichText("🏥 Relief Center: ", contribution.centerName),
                                  buildRichText("📦 Quantity: ", "${contribution.quantity} ${contribution.unit}"),
                                  buildRichText("📝 Description: ", contribution.description ?? 'N/A'),
                                  buildRichText("🔁 Type: ", getType(contribution.type)),                              ],
                              ),
                            ),
                          ),
                        );
                      }).toList(),
                    ),
            ],
          ),
        ),
      ),
    );
  }

  RichText buildRichText(String label, String value) {
    return RichText(
      text: TextSpan(
        style: TextStyle(color: Colors.grey.shade800),
        children: [
          TextSpan(
            text: label,
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
          TextSpan(text: value),
        ],
      ),
    );
  }
}